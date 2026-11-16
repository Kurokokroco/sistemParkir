<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\TransaksiParkir;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Laravel\Mcp\Request;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $awalHariIni = now()->startOfDay();
        $awalBesok = $awalHariIni->copy()->addDay();

        $transaksiHariIni = TransaksiParkir::query()
            ->where('waktu_masuk', '>=', $awalHariIni)
            ->where('waktu_masuk', '<', $awalBesok);

        $areaParkir = AreaParkir::query()
            ->withCount([
                'transaksiParkir as kendaraan_aktif_count' => fn (Builder $query) => $query->whereNull('waktu_keluar'),
            ])
            ->orderBy('nama_area')
            ->get();

        $transaksiTerbaru = TransaksiParkir::query()
            ->with([
                'kendaraan:id,plat_nomor,jenis_kendaraan',
                'areaParkir:id,nama_area',
            ])
            ->latest('waktu_masuk')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'kendaraanAktifCount' => TransaksiParkir::query()->whereNull('waktu_keluar')->count(),
            'transaksiHariIniCount' => $transaksiHariIni->count(),
            'pendapatanHariIni' => TransaksiParkir::query()
                ->whereNotNull('waktu_keluar')
                ->where('waktu_keluar', '>=', $awalHariIni)
                ->where('waktu_keluar', '<', $awalBesok)
                ->sum('biaya'),
            'areaParkir' => $areaParkir,
            'transaksiTerbaru' => $transaksiTerbaru,
        ]);
    }
}
