<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\OrtuController;
use App\Http\Controllers\SantriController;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [LoginController::class, 'dashboard'])->middleware('admin.auth');
Route::get('/santri/dashboard', [LoginController::class, 'dashboard'])->middleware('santri.auth');
Route::get('/ortu/dashboard', [LoginController::class, 'dashboard'])->middleware('ortu.auth');


Route::get('/admin/dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/santri/dashboard', [LoginController::class, 'dashboard'])->name('santri.dashboard');
Route::get('/ortu/dashboard', [LoginController::class, 'dashboard'])->name('ortu.dashboard');

/// Jadwal Kegiatan Routes
Route::get('/admin/jadwalKegiatan', [JadwalKegiatanController::class, 'index'])->name('admin.jadwal');
Route::post('/admin/jadwalKegiatan', [JadwalKegiatanController::class, 'store'])->name('jadwal.store');
Route::put('/admin/jadwalKegiatan/{id}', [JadwalKegiatanController::class, 'update'])->name('jadwal.update');
Route::post('/admin/jadwalKegiatan/delete', [JadwalKegiatanController::class, 'destroy'])->name('jadwal.delete');
Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.baca_semua');

Route::get('/santri/jadwalKegiatan', [JadwalKegiatanController::class, 'index'])->name('santri.jadwal');
Route::get('/ortu/jadwalKegiatan', [JadwalKegiatanController::class, 'index'])->name('ortu.jadwal');



// User santri
Route::get('/admin/santri', [AdminController::class, 'showSantri'])->name('admin.santri');
Route::post('/admin/santri/add', [AdminController::class, 'store'])->name('admin_santri.store');
Route::put('/admin/santri/{id}', [AdminController::class, 'update'])->name('admin_santri.update');
Route::post('/admin/santri/delete', [AdminController::class, 'destroy'])->name('admin_santri.delete');
Route::post('/admin/reset-password', [AdminController::class, 'resetPassword'])->name('admin_santri.reset_password');

// User Ortu
Route::get('/admin/ortu', [AdminController::class, 'showOrtu'])->name('admin.ortu');
Route::post('/admin/ortu/add', [AdminController::class, 'store_ortu'])->name('admin_ortu.store');
Route::put('/admin/ortu/update/{id}', [AdminController::class, 'update_ortu'])->name('admin_ortu.update');
Route::post('/admin/ortu/delete', [AdminController::class, 'destroy_ortu'])->name('admin_ortu.delete');
Route::post('/admin/ortu/reset-password', [AdminController::class, 'resetPassword_ortu'])->name('admin_ortu.reset_password');

// Profile
Route::get('/profile', [LoginController::class, 'showProfile'])->name('profile');
Route::put('/profile/update', [LoginController::class, 'profileUpdate'])->name('profile.update');

// Laporan
Route::get('/santri/laporan', [SantriController::class, 'showLaporan'])->name('santri.laporan');
Route::post('/santri/laporan/upload', [SantriController::class, 'uploadLaporan'])->name('laporan.upload');
Route::put('/santri/laporan/update', [SantriController::class, 'update'])->name('laporan.update');
Route::get('/santri/rekap/laporan', [SantriController::class, 'showRekapLaporan'])->name('santri.rekapLaporan');
Route::get('/santri/laporan/rekapType', [SantriController::class, 'typeLaporan'])->name('santri.rekapType');


Route::get('/ortu/laporan', [OrtuController::class, 'showLaporan'])->name('ortu.laporan');
Route::get('/ortu/laporan/rekapType', [OrtuController::class, 'typeLaporan'])->name('ortu.rekapType');



Route::get('/admin/laporanMasuk', [AdminController::class, 'laporanMasuk'])->name('laporanMasuk');
Route::get('/admin/rekap/laporan', [AdminController::class, 'showRekapLaporan'])->name('admin.rekapLaporan');
Route::get('/admin/rekap/laporan/detail/{santri_id}', [AdminController::class, 'showDetailLaporan'])->name('admin.detailLaporan');


Route::post('/admin/send-rekap/{santri}', [AdminController::class, 'sendRekapPdf'])->name('admin.send.rekap');
Route::get('/admin/laporan/rekapType', [AdminController::class, 'typeLaporan'])->name('admin.rekapType');

// Route::get('/admin/rekap/laporan/pdf/{santri_id}', [AdminController::class, 'generatePdfRekap'])->name('admin.rekap.pdf');

// web.php
Route::get('/download-rekap/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->download($path, $filename, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]);
});


Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');


// // Route::resource('/users', UserController::class);
// Route::post('/users', [UserController::class, 'store'])->name('users.store');
// Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
// // Hapus user
// Route::post('/user/delete', [UserController::class, 'destroy'])->name('user.delete');
// Route::post('/user/reset-password', [UserController::class, 'resetPassword'])->name('user.reset_password');
