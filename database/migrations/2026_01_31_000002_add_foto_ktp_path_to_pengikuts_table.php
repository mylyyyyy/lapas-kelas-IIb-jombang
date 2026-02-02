<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengikuts', function (Blueprint $table) {
            $table->string('foto_ktp_path')->nullable()->after('foto_ktp');
            $table->timestamp('foto_ktp_processed_at')->nullable()->after('foto_ktp_path');
        });
    }

    public function down()
    {
        Schema::table('pengikuts', function (Blueprint $table) {
            $table->dropColumn(['foto_ktp_path', 'foto_ktp_processed_at']);
        });
    }
};