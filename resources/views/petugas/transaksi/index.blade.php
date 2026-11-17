<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Parkir</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-sm navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('petugas.dashboard') }}">Sistem Parkir</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container py-4">
        <h1>Transaksi Parkir</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
            </div>

        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5">Catat Kendaraan Masuk</h2>
                @if ($kendaraanTersedia->isEmpty())
                    <p class="mb-0 text-muted">Tidak ada kendaraan yang tersedia untuk dicatat masuk.</p>
                @elseif ($areaParkirTersedia->isEmpty())
                    <p class="mb-0 text-muted">Tidak ada area parkir yang tersedia. Hubungi admin untuk menambah atau memperbesar area.</p>
                @else
                    <form method="POST" action="{{ route('petugas.transaksi.store') }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-5">
                            <label for="kendaraan_id" class="form-label">Kendaraan</label>
                            <select name="kendaraan_id" id="kendaraan_id" class="form-select @error('kendaraan_id') is-invalid @enderror" required>
                                <option value="">Pilih kendaraan</option>
                                @foreach ($kendaraanTersedia as $kendaraan)
                                    <option value="{{ $kendaraan->id }}" @selected(old('kendaraan_id') == $kendaraan->id)>
                                        {{ $kendaraan->plat_nomor }} - {{ ucfirst($kendaraan->jenis_kendaraan) }}{{ $kendaraan->merk ? ' - '.$kendaraan->merk : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kendaraan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <label for="area_parkir_id" class="form-label">Area Parkir</label>
                            <small class="form-text text-muted">Pilih area dengan jenis yang sama seperti kendaraan.</small>
                            <select name="area_parkir_id" id="area_parkir_id" class="form-select @error('area_parkir_id') is-invalid @enderror" required>
                                <option value="">Pilih area</option>
                                @foreach ($areaParkirTersedia as $area)
                                    <option value="{{ $area->id }}" @selected(old('area_parkir_id') == $area->id)>
                                        {{ $area->nama_area }} - {{ ucfirst($area->jenis_kendaraan) }} ({{ $area->kendaraan_aktif_count }}/{{ $area->kapasitas }})
                                    </option>
                                @endforeach
                            </select>
                            @error('area_parkir_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">Catat Masuk</button>
                        </div>
                    </form>
                @endif
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h5">Riwayat Transaksi</h2>
                <form method="GET" action="{{ route('petugas.transaksi.index') }}" class="mb-3">
                    <label for="transaction-search" class="form-label">Cari transaksi</label>
                    <div class="input-group">
                        <input type="search" name="search" id="transaction-search" class="form-control" value="{{ $search }}" placeholder="Plat nomor, jenis kendaraan, atau area...">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if ($search !== '')
                            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No.</th>
                                <th>Plat Nomor</th>
                                <th>Jenis</th>
                                <th>Area</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
                                <th>Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksiParkir as $transaksi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaksi->kendaraan->plat_nomor }}</td>
                                    <td>{{ ucfirst($transaksi->kendaraan->jenis_kendaraan) }}</td>
                                    <td>{{ $transaksi->areaParkir?->nama_area ?? '-' }}</td>
                                    <td>{{ $transaksi->waktu_masuk->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transaksi->waktu_keluar?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $transaksi->biaya === null ? '-' : 'Rp '.number_format($transaksi->biaya, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($transaksi->waktu_keluar === null)
                                            <span class="badge bg-warning text-dark">Di area parkir</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if ($transaksi->waktu_keluar === null)
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#konfirmasiKeluar-{{ $transaksi->id }}">Catat Keluar</button>
                                            <div class="modal fade" id="konfirmasiKeluar-{{ $transaksi->id }}" tabindex="-1" aria-labelledby="judulKonfirmasiKeluar-{{ $transaksi->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h2 class="modal-title fs-5" id="judulKonfirmasiKeluar-{{ $transaksi->id }}">Konfirmasi Kendaraan Keluar</h2>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">Catat kendaraan {{ $transaksi->kendaraan->plat_nomor }} keluar dari area parkir?</div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form method="POST" action="{{ route('petugas.transaksi.checkout', $transaksi) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success">Ya, Catat Keluar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <a href="{{ route('petugas.transaksi.receipt', $transaksi) }}" class="btn btn-outline-primary btn-sm">Struk</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        {{ $search !== '' ? 'Transaksi tidak ditemukan.' : 'Belum ada transaksi parkir.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <script src="{{ asset('resources/js/bootstrap.min.js') }}"></script>
</body>
</html>
