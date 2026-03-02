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
        Schema::table('wbps', function (Blueprint $table) {
            $table->renameColumn('kamar', 'lokasi_sel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wbps', function (Blueprint $table) {
            $table->renameColumn('lokasi_sel', 'kamar');
        });
    }
};
