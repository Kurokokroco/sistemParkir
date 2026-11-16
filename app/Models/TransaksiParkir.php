<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiParkir extends Model
{
    protected $table = 'transaksi_parkir';

    protected $fillable = [
        'kendaraan_id',
        'area_parkir_id',
        'waktu_masuk',
        'waktu_keluar',
        'biaya',
    ];

    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'waktu_keluar' => 'datetime',
            'biaya' => 'integer',
        ];
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function areaParkir(): BelongsTo
    {
        return $this->belongsTo(AreaParkir::class);
    }

    public function tarifPerJam(): int
    {
        return match ($this->kendaraan->jenis_kendaraan) {
            'motor' => 2000,
            'mobil' => 5000,
        };
    }

    public function durasiTagihanJam(DateTimeInterface $waktuKeluar): int
    {
        $durasiDetik = max(0, $waktuKeluar->getTimestamp() - $this->waktu_masuk->getTimestamp());

        return max(1, (int) ceil($durasiDetik / 3600));
    }

    public function hitungBiaya(DateTimeInterface $waktuKeluar): int
    {
        return $this->durasiTagihanJam($waktuKeluar) * $this->tarifPerJam();
    }
}
