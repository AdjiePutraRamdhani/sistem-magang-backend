<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranMagang;
use App\Models\Pembimbing;
use App\Models\Sertifikat;

class LandingController extends Controller
{
    public function overview()
    {
        return response()->json([
            'total_pendaftaran' => PendaftaranMagang::count(),

            'menunggu_verifikasi' =>
                PendaftaranMagang::where('status', 'pending')->count(),

            'pembimbing_aktif' =>
                Pembimbing::count(),

            'sertifikat_terbit' =>
                Sertifikat::count(),
        ]);
    }
}