<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AreaParkirController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\TransaksiParkirController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::get('/transaksi', [TransaksiParkirController::class, 'index'])->name('transaksi.index');

    Route::prefix('/area-parkir')->name('area_parkir.')->group(function () {
        Route::get('/', [AreaParkirController::class, 'index'])->name('index');
        Route::get('/create', [AreaParkirController::class, 'create'])->name('create');
        Route::post('/', [AreaParkirController::class, 'store'])->name('store');
        Route::get('/{areaParkir}/edit', [AreaParkirController::class, 'edit'])->name('edit');
        Route::put('/{areaParkir}', [AreaParkirController::class, 'update'])->name('update');
        Route::delete('/{areaParkir}', [AreaParkirController::class, 'destroy'])->name('destroy');
    });

    Route::get('/pengguna/create', [UserController::class, 'create'])
        ->name('pengguna.create');
    Route::post('/pengguna', [UserController::class, 'store'])
        ->name('pengguna.store');
    Route::get('/pengguna', [UserController::class, 'index'])
        ->name('pengguna.index');
    Route::get('/pengguna/{pengguna}/edit', [UserController::class, 'edit'])
        ->name('pengguna.edit');
    Route::put('/pengguna/{pengguna}', [UserController::class, 'update'])
        ->name('pengguna.update');
    Route::delete('/pengguna/{pengguna}', [UserController::class, 'destroy'])
        ->name('pengguna.destroy');

    Route::resource('kendaraan', KendaraanController::class)->except(['show']);
});

Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', fn () => view('petugas.dashboard'))->name('dashboard');
    Route::get('/transaksi', [TransaksiParkirController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiParkirController::class, 'store'])->name('transaksi.store');
    Route::patch('/transaksi/{transaksiParkir}/checkout', [TransaksiParkirController::class, 'checkout'])
        ->name('transaksi.checkout');
    Route::get('/transaksi/{transaksiParkir}/struk', [TransaksiParkirController::class, 'receipt'])
        ->name('transaksi.receipt');
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', fn () => view('owner.dashboard'))->name('dashboard');
});
