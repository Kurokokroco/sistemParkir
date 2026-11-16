<?php

namespace App\Models;

use Database\Factories\AreaParkirFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaParkir extends Model
{
    protected $table = 'area_parkir';

    protected $fillable = [
        'nama_area',
        'jenis_kendaraan',
        'kapasitas',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
        ];
    }

    /** @use HasFactory<AreaParkirFactory> */
    use HasFactory;

    public function transaksiParkir(): HasMany
    {
        return $this->hasMany(TransaksiParkir::class);
    }
}
