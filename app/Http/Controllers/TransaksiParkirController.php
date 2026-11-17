<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\TransaksiParkir;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransaksiParkirController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ])['search'] ?? '';

        $query = TransaksiParkir::query()->with(['kendaraan', 'areaParkir']);

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->whereHas('kendaraan', function (Builder $query) use ($search): void {
                    $query->where('plat_nomor', 'like', "%{$search}%")
                        ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
                })->orWhereHas('areaParkir', function (Builder $query) use ($search): void {
                    $query->where('nama_area', 'like', "%{$search}%");
                });
            });
        }

        $transaksiParkir = $query->latest('waktu_masuk')->get();

        if ($request->user()->role === 'admin') {
            return view('admin.transaksi.index', compact('transaksiParkir', 'search'));
        }

        $kendaraanTersedia = Kendaraan::query()
            ->whereDoesntHave('transaksiParkir', function ($query): void {
                $query->whereNull('waktu_keluar');
            })
            ->orderBy('plat_nomor')
            ->get();
        $areaParkirTersedia = AreaParkir::query()
            ->withCount([
                'transaksiParkir as kendaraan_aktif_count' => fn (Builder $query) => $query->whereNull('waktu_keluar'),
            ])
            ->orderBy('nama_area')
            ->get()
            ->filter(fn (AreaParkir $areaParkir): bool => $areaParkir->kendaraan_aktif_count < $areaParkir->kapasitas)
            ->values();

        return view('petugas.transaksi.index', compact('transaksiParkir', 'kendaraanTersedia', 'areaParkirTersedia', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kendaraan_id' => ['required', 'integer', 'exists:kendaraan,id'],
            'area_parkir_id' => ['required', 'integer', 'exists:area_parkir,id'],
        ]);

        DB::transaction(function () use ($data): void {
            $areaParkir = AreaParkir::query()
                ->lockForUpdate()
                ->findOrFail($data['area_parkir_id']);
            $kendaraan = Kendaraan::query()
                ->lockForUpdate()
                ->findOrFail($data['kendaraan_id']);

            if ($kendaraan->jenis_kendaraan !== $areaParkir->jenis_kendaraan) {
                throw ValidationException::withMessages([
                    'area_parkir_id' => 'Jenis area parkir tidak sesuai dengan kendaraan.',
                ]);
            }

            if ($areaParkir->transaksiParkir()->whereNull('waktu_keluar')->count() >= $areaParkir->kapasitas) {
                throw ValidationException::withMessages([
                    'area_parkir_id' => 'Area parkir sudah penuh.',
                ]);
            }

            if ($kendaraan->transaksiParkir()->whereNull('waktu_keluar')->exists()) {
                throw ValidationException::withMessages([
                    'kendaraan_id' => 'Kendaraan ini masih tercatat berada di area parkir.',
                ]);
            }

            $areaParkir->transaksiParkir()->create([
                'kendaraan_id' => $kendaraan->id,
                'waktu_masuk' => now(),
            ]);
        });

        return redirect()
            ->route('petugas.transaksi.index')
            ->with('success', 'Kendaraan berhasil dicatat masuk.');
    }

    public function checkout(TransaksiParkir $transaksiParkir): RedirectResponse
    {
        if ($transaksiParkir->waktu_keluar !== null) {
            return back()->with('error', 'Transaksi ini sudah selesai.');
        }

        $transaksiParkir->loadMissing(['kendaraan', 'areaParkir']);
        $waktuKeluar = now();

        $transaksiParkir->update([
            'waktu_keluar' => $waktuKeluar,
            'biaya' => $transaksiParkir->hitungBiaya($waktuKeluar),
        ]);

        return redirect()
            ->route('petugas.transaksi.index')
            ->with('success', 'Kendaraan berhasil dicatat keluar.');
    }

    public function receipt(TransaksiParkir $transaksiParkir): View
    {
        $transaksiParkir->loadMissing(['kendaraan', 'areaParkir']);

        return view('petugas.transaksi.struk', [
            'transaksiParkir' => $transaksiParkir,
            'durasiTagihanJam' => $transaksiParkir->waktu_keluar === null
                ? null
                : $transaksiParkir->durasiTagihanJam($transaksiParkir->waktu_keluar),
            'tarifPerJam' => $transaksiParkir->tarifPerJam(),
        ]);
    }
}
