<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'plat_nomor',
        'jenis_kendaraan',
        'merk',
    ];

    public function transaksiParkir(): HasMany
    {
        return $this->hasMany(TransaksiParkir::class);
    }
}
