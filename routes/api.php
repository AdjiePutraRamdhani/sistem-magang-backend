<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\PendaftaranController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------------------
// ROUTE PUBLIK
// ----------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ----------------------------------------------------------------
// ROUTE TERPROTEKSI
// ----------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ADMIN
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard',                 [AdminController::class, 'dashboard']);
        Route::get('/mahasiswa',                 [AdminController::class, 'indexMahasiswa']);
        Route::delete('/mahasiswa/{id}',         [AdminController::class, 'destroyMahasiswa']);
        Route::get('/pembimbing',                [AdminController::class, 'indexPembimbing']);
        Route::post('/pembimbing',               [AdminController::class, 'storePembimbing']);
        Route::get('/pendaftaran',               [PendaftaranController::class, 'index']);
        Route::post('/pendaftaran/{id}/setujui', [PendaftaranController::class, 'setujui']);
        Route::post('/pendaftaran/{id}/tolak',   [PendaftaranController::class, 'tolak']);
    });

    // MAHASISWA
    Route::prefix('mahasiswa')->group(function () {
        Route::get('/dashboard',  [MahasiswaController::class, 'dashboard']);
        Route::post('/daftar',    [MahasiswaController::class, 'daftar']);
        Route::get('/sertifikat', [MahasiswaController::class, 'sertifikat']);
    });

    // PEMBIMBING — akan ditambahkan di Tahap 6
});