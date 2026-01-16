<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Assign 'superadmin' role to the first user (e.g., user with ID 1)
        User::where('id', 1)->update(['role' => 'superadmin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the role of user with ID 1 back to 'admin' or 'user' if it was 'superadmin'
        // Assuming 'admin' was a common role, or 'user' as default.
        // This is a simple revert, adjust based on your application's logic.
        User::where('id', 1)->where('role', 'superadmin')->update(['role' => 'admin']);
    }
};
