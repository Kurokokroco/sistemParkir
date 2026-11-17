<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AreaParkirController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ])['search'] ?? '';

        $query = AreaParkir::query()
            ->withCount([
                'transaksiParkir as kendaraan_aktif_count' => fn (Builder $query) => $query->whereNull('waktu_keluar'),
            ]);

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('nama_area', 'like', "%{$search}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        $areaParkir = $query->orderBy('nama_area')->get();

        return view('admin.area_parkir.index', compact('areaParkir', 'search'));
    }

    public function create(): View
    {
        return view('admin.area_parkir.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_area' => ['required', 'string', 'max:255', 'unique:area_parkir,nama_area'],
            'jenis_kendaraan' => ['required', Rule::in(['motor', 'mobil'])],
            'kapasitas' => ['required', 'integer', 'min:1'],
        ]);

        AreaParkir::create($data);

        return redirect()
            ->route('admin.area_parkir.index')
            ->with('success', 'Area parkir berhasil ditambahkan.');
    }

    public function edit(AreaParkir $areaParkir): View
    {
        return view('admin.area_parkir.edit', compact('areaParkir'));
    }

    public function update(Request $request, AreaParkir $areaParkir): RedirectResponse
    {
        $data = $request->validate([
            'nama_area' => [
                'required',
                'string',
                'max:255',
                Rule::unique('area_parkir', 'nama_area')->ignore($areaParkir->id),
            ],
            'jenis_kendaraan' => ['required', Rule::in(['motor', 'mobil'])],
            'kapasitas' => ['required', 'integer', 'min:1'],
        ]);

        $kendaraanAktifCount = $areaParkir->transaksiParkir()
            ->whereNull('waktu_keluar')
            ->count();

        if ($data['jenis_kendaraan'] !== $areaParkir->jenis_kendaraan && $kendaraanAktifCount > 0) {
            return back()
                ->withErrors(['jenis_kendaraan' => 'Jenis kendaraan tidak dapat diubah saat area masih terisi.'])
                ->withInput();
        }

        if ($data['kapasitas'] < $kendaraanAktifCount) {
            return back()
                ->withErrors(['kapasitas' => 'Kapasitas tidak boleh lebih kecil dari kendaraan yang sedang parkir.'])
                ->withInput();
        }

        $areaParkir->update($data);

        return redirect()
            ->route('admin.area_parkir.index')
            ->with('success', 'Area parkir berhasil diperbarui.');
    }

    public function destroy(AreaParkir $areaParkir): RedirectResponse
    {
        if ($areaParkir->transaksiParkir()->exists()) {
            return back()->with('error', 'Area tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $areaParkir->delete();

        return redirect()
            ->route('admin.area_parkir.index')
            ->with('success', 'Area parkir berhasil dihapus.');
    }
}
