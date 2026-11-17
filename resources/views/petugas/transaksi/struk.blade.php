<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 480px">
            <div class="card-body">
                <h1 class="h4 text-center">Struk Parkir</h1>
                <hr>
                <dl class="row mb-0">
                    <dt class="col-5">Plat Nomor</dt>
                    <dd class="col-7">{{ $transaksiParkir->kendaraan->plat_nomor }}</dd>
                    <dt class="col-5">Jenis</dt>
                    <dd class="col-7">{{ ucfirst($transaksiParkir->kendaraan->jenis_kendaraan) }}</dd>
                    <dt class="col-5">Merk</dt>
                    <dd class="col-7">{{ $transaksiParkir->kendaraan->merk ?? '-' }}</dd>
                    <dt class="col-5">Area Parkir</dt>
                    <dd class="col-7">{{ $transaksiParkir->areaParkir?->nama_area ?? '-' }}</dd>
                    <dt class="col-5">Waktu Masuk</dt>
                    <dd class="col-7">{{ $transaksiParkir->waktu_masuk->format('d/m/Y H:i') }}</dd>
                    <dt class="col-5">Waktu Keluar</dt>
                    <dd class="col-7">{{ $transaksiParkir->waktu_keluar?->format('d/m/Y H:i') ?? 'Belum keluar' }}</dd>
                    <dt class="col-5">Tarif per jam</dt>
                    <dd class="col-7">Rp {{ number_format($tarifPerJam, 0, ',', '.') }}</dd>
                    <dt class="col-5">Durasi tagihan</dt>
                    <dd class="col-7">{{ $durasiTagihanJam === null ? '-' : $durasiTagihanJam.' jam' }}</dd>
                    <dt class="col-5">Total biaya</dt>
                    <dd class="col-7 fw-bold">
                        {{ $transaksiParkir->biaya === null ? 'Belum dihitung' : 'Rp '.number_format($transaksiParkir->biaya, 0, ',', '.') }}
                    </dd>
                </dl>
                <hr>
                <p class="text-center mb-0">Terima kasih telah menggunakan layanan parkir.</p>
            </div>
        </div>
        <div class="text-center mt-3 no-print">
            <p>Untuk mencetak struk, gunakan Ctrl+P .</p>
            <a href="{{ route('petugas.transaksi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </main>
</body>
</html>
