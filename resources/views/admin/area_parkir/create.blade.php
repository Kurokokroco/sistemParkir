<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Area Parkir</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h1 class="h3">Tambah Area Parkir</h1>
                        <form action="{{ route('admin.area_parkir.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama_area" class="form-label">Nama Area</label>
                                <input type="text" name="nama_area" id="nama_area" class="form-control @error('nama_area') is-invalid @enderror" value="{{ old('nama_area') }}" required>
                                @error('nama_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-select @error('jenis_kendaraan') is-invalid @enderror" required>
                                    <option value="">Pilih jenis</option>
                                    @foreach (['motor' => 'Motor', 'mobil' => 'Mobil'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('jenis_kendaraan') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kendaraan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas</label>
                                <input type="number" min="1" name="kapasitas" id="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" value="{{ old('kapasitas') }}" required>
                                @error('kapasitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.area_parkir.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
