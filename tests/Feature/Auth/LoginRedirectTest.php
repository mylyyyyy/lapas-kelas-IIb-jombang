<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_with_api_intended_gets_redirected_to_user_page()
    {
        $user = User::factory()->create(['role' => 'pengunjung']);

        $response = $this->withSession(['url.intended' => url('/api/admin/antrian/state')])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticatedAs($user);

        // If route exists, prefer kunjungan.riwayat, otherwise fallback to '/'
        if (route_exists('kunjungan.riwayat')) {
            $response->assertRedirect(route('kunjungan.riwayat', false));
        } else {
            $response->assertRedirect('/');
        }
    }

    public function test_admin_with_api_intended_is_redirected_to_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->withSession(['url.intended' => url('/api/admin/antrian/state')])
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard', false));
    }
}


// Small helper used within the test file (kept local to avoid touching TestCase)
if (!function_exists('route_exists')) {
    function route_exists($name)
    {
        try {
            return \Illuminate\Support\Facades\Route::has($name);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
