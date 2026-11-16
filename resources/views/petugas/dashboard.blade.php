<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-sm navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('petugas.dashboard') }}">Sistem Parkir</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('petugas.transaksi.index') }}">Transaksi Parkir</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container py-4">
        <h1>Dashboard Petugas</h1>
        <p>Kelola kendaraan masuk dan keluar area parkir.</p>
        <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-primary">Buka Transaksi Parkir</a>
    </main>
</body>
</html>