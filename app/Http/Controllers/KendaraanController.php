<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ])['search'] ?? '';

        $query = Kendaraan::query();

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('plat_nomor', 'like', "%{$search}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $kendaraan = $query->latest()->get();

        return view('admin.kendaraan.index', compact('kendaraan', 'search'));
    }

    public function create(): View
    {
        return view('admin.kendaraan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plat_nomor' => ['required', 'string', 'max:255', 'unique:kendaraan,plat_nomor'],
            'jenis_kendaraan' => ['required', Rule::in(['motor', 'mobil'])],
            'merk' => ['nullable', 'string', 'max:255'],
        ]);

        Kendaraan::create($data);

        return redirect()
            ->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan): View
    {
        return view('admin.kendaraan.edit', compact('kendaraan'));
    }

    public function update(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $data = $request->validate([
            'plat_nomor' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kendaraan', 'plat_nomor')->ignore($kendaraan->id),
            ],
            'jenis_kendaraan' => ['required', Rule::in(['motor', 'mobil'])],
            'merk' => ['nullable', 'string', 'max:255'],
        ]);

        $kendaraan->update($data);

        return redirect()
            ->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan): RedirectResponse
    {
        $kendaraan->delete();

        return redirect()
            ->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}
