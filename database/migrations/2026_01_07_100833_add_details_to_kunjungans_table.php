<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            if (!Schema::hasColumn('kunjungans', 'wbp_id')) {
                $table->foreignId('wbp_id')->nullable()->constrained('wbps')->onDelete('cascade');
            }
            if (!Schema::hasColumn('kunjungans', 'total_pengikut')) {
                $table->integer('total_pengikut')->default(0);
            }
            if (!Schema::hasColumn('kunjungans', 'data_pengikut')) {
                $table->json('data_pengikut')->nullable(); // Simpan array nama & barang
            }
        });
    }

    public function down()
    {
        Schema::table('kunjungans', function (Blueprint $table) {
            if (Schema::hasColumn('kunjungans', 'wbp_id')) {
                $table->dropForeign(['wbp_id']);
                $table->dropColumn('wbp_id');
            }
            if (Schema::hasColumn('kunjungans', 'total_pengikut')) {
                $table->dropColumn('total_pengikut');
            }
            if (Schema::hasColumn('kunjungans', 'data_pengikut')) {
                $table->dropColumn('data_pengikut');
            }
        });
    }
};
