<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Parkir</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-sm navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Sistem Parkir</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('admin.pengguna.index') }}">Pengguna</a>
                <a class="nav-link" href="{{ route('admin.kendaraan.index') }}">Kendaraan</a>
                <a class="nav-link active" href="{{ route('admin.area_parkir.index') }}">Area Parkir</a>
                <a class="nav-link" href="{{ route('admin.transaksi.index') }}">Transaksi</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
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
            <div class="card-body table-responsive">
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
                                    <form action="{{ route('admin.area_parkir.destroy', $area) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus area ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada area parkir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
