<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Transaksi</title>
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
                <a class="nav-link" href="{{ route('admin.area_parkir.index') }}">Area Parkir</a>
                <a class="nav-link active" href="{{ route('admin.transaksi.index') }}">Transaksi</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container py-4">
        <h1>Monitoring Transaksi</h1>
        <p class="text-muted">Halaman ini hanya untuk melihat transaksi. Pencatatan kendaraan masuk dan keluar dilakukan oleh petugas.</p>

        <section class="card border-0 shadow-sm">
            <div class="card-body">
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
                                <th>Status</th>
                                <th>Biaya</th>
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
                                    <td>
                                        @if ($transaksi->waktu_keluar === null)
                                            <span class="badge bg-warning text-dark">Di area parkir</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaksi->biaya === null ? '-' : 'Rp '.number_format($transaksi->biaya, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada transaksi parkir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
