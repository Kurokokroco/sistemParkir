<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Halaman pengguna</title>
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
                        <a class="nav-link  active" href="">Pengguna</a>
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

    <div class="container py-4">
        <h1>Pengguna</h1>
        <p class="text-muted">Keloal pengguna di sini</p>
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
        <a href="{{ route('admin.pengguna.create') }}" class="btn btn-primary btn-sm w-auto mb-3">Tambah Pengguna</a>
        <div class="card border-0 shadow-sm">


            <div class="card-body">
                <form method="GET" action="{{ route('admin.pengguna.index') }}" class="mb-3">
                    <label for="user-search" class="form-label">Cari pengguna</label>
                    <div class="input-group">
                        <input type="search" name="search" id="user-search" class="form-control" value="{{ $search }}" placeholder="Nama, email, atau role...">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if ($search !== '')
                            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <td>No.</td>
                                <td>Nama</td>
                                <td>Email</td>
                                <td>Role.</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        <span class="badge bg-primary text-white">
                                            {{ $item->role }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pengguna.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#konfirmasiHapusPengguna-{{ $item->id }}">Hapus</button>
                                        <div class="modal fade" id="konfirmasiHapusPengguna-{{ $item->id }}" tabindex="-1" aria-labelledby="judulHapusPengguna-{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h2 class="modal-title fs-5" id="judulHapusPengguna-{{ $item->id }}">Konfirmasi Hapus Pengguna</h2>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">Yakin ingin menghapus pengguna {{ $item->name }}?</div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('admin.pengguna.destroy', $item) }}" method="POST">
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
                                <td class="text-center" colspan="5">
                                    {{ $search !== '' ? 'Pengguna tidak ditemukan.' : 'Belum ada data' }}
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