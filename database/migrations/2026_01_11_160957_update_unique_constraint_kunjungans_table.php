<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            // 1. Hapus aturan unik yang lama (Tanggal + Nomor)
            // Nama index biasanya: table_column_unique. Cek error log kamu: 'kunjungans_tanggal_kunjungan_nomor_antrian_harian_unique'
            $table->dropUnique('kunjungans_tanggal_kunjungan_nomor_antrian_harian_unique');

            // 2. Buat aturan unik yang baru (Tanggal + Sesi + Nomor)
            // Jadi: Tgl 12, Pagi, No 1 (Boleh) DAN Tgl 12, Siang, No 1 (Boleh)
            $table->unique(['tanggal_kunjungan', 'sesi', 'nomor_antrian_harian'], 'kunjungan_unik_per_sesi');
        });
    }

    public function down()
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            $table->dropUnique('kunjungan_unik_per_sesi');
            $table->unique(['tanggal_kunjungan', 'nomor_antrian_harian']);
        });
    }
};
