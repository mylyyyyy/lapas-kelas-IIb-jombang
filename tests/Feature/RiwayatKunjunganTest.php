<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kunjungan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatKunjunganTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_visit_history()
    {
        $user = User::factory()->create();
        Kunjungan::factory()->count(5)->create([
            'email_pengunjung' => $user->email,
        ]);
        Kunjungan::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('kunjungan.riwayat'));

        $response->assertStatus(200);
        $response->assertViewIs('guest.kunjungan.riwayat');
        $response->assertViewHas('kunjungans', function ($kunjungans) use ($user) {
            return $kunjungans->count() === 5 && $kunjungans->every(function ($kunjungan) use ($user) {
                return $kunjungan->email_pengunjung === $user->email;
            });
        });
    }

    public function test_guest_cannot_view_visit_history()
    {
        $response = $this->get(route('kunjungan.riwayat'));

        $response->assertRedirect(route('login'));
    }
}
