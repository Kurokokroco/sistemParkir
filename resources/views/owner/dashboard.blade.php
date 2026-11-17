<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard owner</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body class="bg-light">

    <div class="container py-4">
        <h1>Dashboard Owner</h1>
        <p class="text-muted">Selamat datang di dashboard owner.</p>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <td>No.</td>
                                <td>Plat Nomor</td>
                                <td>Waktu Masuk</td>
                                <td>Waktu Keluar</td>
                                <td>Biaya</td>
                                <td>No.</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('resources/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>