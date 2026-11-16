<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard admin</title>
     <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body>
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
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}" aria-current="page"
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
                        <a class="nav-link" href="{{ route('admin.area_parkir.index') }}">Area Parkir</a>
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
        <div class="mb-4">
            <h1>Dashboard Admin</h1>
            <p class="text-muted mb-0">Ringkasan kondisi parkir hari ini.</p>
        </div>

        <section class="row g-3 mb-4" aria-label="Ringkasan hari ini">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted">Kendaraan sedang parkir</h2>
                        <p class="display-6 fw-semibold mb-0">{{ number_format($kendaraanAktifCount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted">Transaksi masuk hari ini</h2>
                        <p class="display-6 fw-semibold mb-0">{{ number_format($transaksiHariIniCount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted">Pendapatan hari ini</h2>
                        <p class="display-6 fw-semibold mb-0">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5">Kapasitas Area Parkir</h2>
                        @forelse ($areaParkir as $area)
                            @php
                                $persentaseTerisi = min(100, (int) round($area->kendaraan_aktif_count / $area->kapasitas * 100));
                                $warnaProgress = $persentaseTerisi >= 100
                                    ? 'bg-danger'
                                    : ($persentaseTerisi >= 80 ? 'bg-warning' : 'bg-success');
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong>{{ $area->nama_area }}</strong>
                                        <div class="small text-muted">{{ ucfirst($area->jenis_kendaraan) }}</div>
                                    </div>
                                    <span>{{ $area->kendaraan_aktif_count }} / {{ $area->kapasitas }}</span>
                                </div>
                                <div class="progress" role="progressbar"
                                    aria-label="Kapasitas {{ $area->nama_area }}"
                                    aria-valuenow="{{ $persentaseTerisi }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <div class="progress-bar {{ $warnaProgress }}" style="width: {{ $persentaseTerisi }}%"></div>
                                </div>
                                @if ($persentaseTerisi >= 100)
                                    <span class="badge bg-danger mt-2">Penuh</span>
                                @elseif ($persentaseTerisi >= 80)
                                    <span class="badge bg-warning text-dark mt-2">Hampir penuh</span>
                                @else
                                    <span class="badge bg-success mt-2">Tersedia</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada area parkir yang terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5">Akses Cepat</h2>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-primary">Pengguna</a>
                            <a href="{{ route('admin.kendaraan.index') }}" class="btn btn-outline-primary">Kendaraan</a>
                            <a href="{{ route('admin.area_parkir.index') }}" class="btn btn-outline-primary">Area Parkir</a>
                            <a href="{{ route('admin.transaksi.index') }}" class="btn btn-outline-primary">Monitoring Transaksi</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h5">Transaksi Terbaru</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>No.</th>
                                <th>Plat Nomor</th>
                                <th>Area</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksiTerbaru as $transaksi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaksi->kendaraan->plat_nomor }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada transaksi parkir.</td>
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