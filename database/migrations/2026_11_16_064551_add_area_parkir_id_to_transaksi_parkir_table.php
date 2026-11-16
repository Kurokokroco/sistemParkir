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
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            $table->foreignId('area_parkir_id')
                ->nullable()
                ->after('kendaraan_id')
                ->constrained('area_parkir')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_parkir', function (Blueprint $table) {
            $table->dropForeign(['area_parkir_id']);
            $table->dropColumn('area_parkir_id');
        });
    }
};
