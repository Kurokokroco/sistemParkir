<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use Illuminate\Database\Seeder;

class AreaParkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AreaParkir::firstOrCreate(
            ['nama_area' => 'Area Motor A'],
            ['jenis_kendaraan' => 'motor', 'kapasitas' => 50],
        );

        AreaParkir::firstOrCreate(
            ['nama_area' => 'Area Mobil A'],
            ['jenis_kendaraan' => 'mobil', 'kapasitas' => 20],
        );
    }
}
