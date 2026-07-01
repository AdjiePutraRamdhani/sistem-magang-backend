<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranMagang;
use App\Models\Pembimbing;
use App\Models\Sertifikat;
use Illuminate\Http\Request;

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

    public function peserta(Request $request)
    {
        PendaftaranMagang::syncStatus();

        $query = PendaftaranMagang::whereIn('status', ['disetujui', 'aktif', 'selesai_dinilai', 'sudah_sertifikat'])
            ->with([
                'mahasiswa.user',
                'pembimbing.user',
            ]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('mahasiswa.user', function ($uq) use ($request) {
                    $uq->where('nama_lengkap', 'like', "%{$request->search}%");
                })->orWhereHas('mahasiswa', function ($mq) use ($request) {
                    $mq->where('asal_instansi', 'like', "%{$request->search}%")
                      ->orWhere('program_studi', 'like', "%{$request->search}%");
                });
            });
        }

        $data = $query->latest()->get()->map(function ($item) {
            return [
                'id'              => $item->id,
                'nama_lengkap'    => $item->mahasiswa->user->nama_lengkap ?? '-',
                'asal_instansi'   => $item->mahasiswa->asal_instansi ?? '-',
                'program_studi'   => $item->mahasiswa->program_studi ?? '-',
                'tanggal_mulai'   => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'status'          => $item->status,
                'pembimbing'      => $item->pembimbing
                    ? $item->pembimbing->user->nama_lengkap
                    : null,
            ];
        });

        return response()->json($data);
    }
}