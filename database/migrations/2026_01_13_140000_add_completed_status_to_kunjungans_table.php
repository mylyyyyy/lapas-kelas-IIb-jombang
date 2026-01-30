<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'completed' to the enum. Using a raw statement for MySQL only.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('kunjungans', function (Blueprint $table) {
                DB::statement("ALTER TABLE kunjungans CHANGE COLUMN status status ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending'");
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('kunjungans', function (Blueprint $table) {
                DB::statement("ALTER TABLE kunjungans CHANGE COLUMN status status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
            });
        }
    }
};
