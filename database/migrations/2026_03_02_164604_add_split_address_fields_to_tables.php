<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->string('alamat')->nullable()->after('alamat_pengunjung');
            $table->string('rt', 10)->nullable()->after('alamat');
            $table->string('rw', 10)->nullable()->after('rt');
            $table->string('desa')->nullable()->after('rw');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });

        Schema::table('profil_pengunjungs', function (Blueprint $table) {
            $table->string('rt', 10)->nullable()->after('alamat');
            $table->string('rw', 10)->nullable()->after('rt');
            $table->string('desa')->nullable()->after('rw');
            $table->string('kecamatan')->nullable()->after('desa');
            $table->string('kabupaten')->nullable()->after('kecamatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten']);
        });

        Schema::table('profil_pengunjungs', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw', 'desa', 'kecamatan', 'kabupaten']);
        });
    }
};
