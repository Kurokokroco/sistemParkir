<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Transaksi</title>
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

                </ul>
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <h1>Riwayat Transaksi</h1>
        <section class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('owner.dashboard') }}" class="mb-3">
                    <label for="transaction-search" class="form-label">Cari transaksi</label>
                    <div class="input-group">
                        <input type="search" name="search" id="transaction-search" class="form-control" value="{{ $search }}" placeholder="Plat nomor, jenis kendaraan, atau area...">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if ($search !== '')
                            <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">Reset</a>
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
                                    <td colspan="8" class="text-center">
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
