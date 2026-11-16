<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Kendaraan</title>
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
                        <a class="nav-link" href="{{ route('admin.dashboard') }}" aria-current="page"
                            >Dashboard
                            <span class="visually-hidden">(current)</span></a
                        >
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.pengguna.index') }}">Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.kendaraan.index') }}">Kendaraan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.area_parkir.index') }}">Area Parkir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.transaksi.index') }}">Transaksi</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto me-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="#">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <h3>Data Kendaraan</h3>
            <p class="text-muted">Kelola data kendaraan di sini</p>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}

                    <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}

                    <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
                </div>
            @endif

            
            <div class="card-body">
                <a href="{{ route('admin.kendaraan.create') }}" class="btn btn-primary btn-sm mb-2">Tambah Kendaraan</a>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <td>No.</td>
                                <td>Plat Nomor</td>
                                <td>Jenis Kendaraan</td>
                                <td>Merk</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kendaraan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->plat_nomor }}</td>
                                    <td>{{ ucfirst($item->jenis_kendaraan) }}</td>
                                    <td>{{ $item->merk ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.kendaraan.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.kendaraan.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kendaraan ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    Belum ada data
                                </td>
                            </tr>
                                
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    

     <script src="{{ asset('resources/js/bootstrap.min.js') }}"></script>
</body>
</html>