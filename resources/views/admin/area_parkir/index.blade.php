<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Parkir</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
      <nav
        class="navbar navbar-expand-sm navbar-dark bg-primary"
    >
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Sistem Parkir</a>
            <button
                class="navbar-toggler d-lg-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId"
                aria-controls="collapsibleNavId"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}" aria-current="page"
                            >Dashboard
                            <span class="visually-hidden">(current)</span></a
                        >
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.pengguna.index') }}">Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.kendaraan.index') }}">Kendaraan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.area_parkir.index') }}">Area Parkir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.transaksi.index') }}">Transaksi</a>
                    </li>
                </ul>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <h1>Area Parkir</h1>
        <p class="text-muted">Kelola zona dan kapasitas parkir kendaraan.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <a href="{{ route('admin.area_parkir.create') }}" class="btn btn-primary btn-sm mb-3">Tambah Area</a>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.area_parkir.index') }}" class="mb-3">
                    <label for="area-search" class="form-label">Cari area parkir</label>
                    <div class="input-group">
                        <input type="search" name="search" id="area-search" class="form-control" value="{{ $search }}" placeholder="Nama area atau jenis kendaraan...">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if ($search !== '')
                            <a href="{{ route('admin.area_parkir.index') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Nama Area</th>
                            <th>Jenis Kendaraan</th>
                            <th>Kapasitas Terpakai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areaParkir as $area)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $area->nama_area }}</td>
                                <td>{{ ucfirst($area->jenis_kendaraan) }}</td>
                                <td>{{ $area->kendaraan_aktif_count }} / {{ $area->kapasitas }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.area_parkir.edit', $area) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#konfirmasiHapusArea-{{ $area->id }}">Hapus</button>
                                    <div class="modal fade" id="konfirmasiHapusArea-{{ $area->id }}" tabindex="-1" aria-labelledby="judulHapusArea-{{ $area->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h2 class="modal-title fs-5" id="judulHapusArea-{{ $area->id }}">Konfirmasi Hapus Area</h2>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">Yakin ingin menghapus area {{ $area->nama_area }}?</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('admin.area_parkir.destroy', $area) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    {{ $search !== '' ? 'Area parkir tidak ditemukan.' : 'Belum ada area parkir.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
       <script src="{{ asset('resources/js/bootstrap.min.js') }}"></script>
</body>
</html>
