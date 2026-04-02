<?php

use App\Http\Controllers\Admin\KaryawanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\LaporanController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', [AuthWebController::class, 'process'])
    ->name('login.process');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::resource('siswa', SiswaController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('jadwal', JadwalController::class);
    Route::resource('karyawan', KaryawanController::class);

Route::patch('karyawan/{id}/status', [KaryawanController::class, 'status'])->name('karyawan.status');

    // Route presensi admin belum diaktifkan (controller belum tersedia)
});

Route::middleware(['auth', 'role:tutor'])->prefix('tutor')->name('tutor.')->group(function () {
    Route::view('/dashboard', 'tutor.dashboard')->name('dashboard');
    Route::get('/presensi', [\App\Http\Controllers\Tutor\PresensiFotoController::class, 'index'])->name('presensi');
    Route::post('/presensi', [\App\Http\Controllers\Tutor\PresensiFotoController::class, 'store'])->name('presensi.store');
});
