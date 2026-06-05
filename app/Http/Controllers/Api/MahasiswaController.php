<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranMagang;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    // ----------------------------------------------------------------
    // GET /api/mahasiswa/dashboard
    // ----------------------------------------------------------------
    public function dashboard(Request $request)
    {
        PendaftaranMagang::syncStatus();
        
        $mahasiswa = $request->user()->mahasiswa;

        if (!$mahasiswa) {
            return response()->json(['message' => 'Profil mahasiswa tidak ditemukan.'], 404);
        }

        $pendaftaran = PendaftaranMagang::with(['pembimbing.user', 'penilaian', 'sertifikat'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->first();

        return response()->json([
            'mahasiswa'   => [
                'nama_lengkap'  => $request->user()->nama_lengkap,
                'email'         => $request->user()->email,
                'asal_instansi' => $mahasiswa->asal_instansi,
                'program_studi' => $mahasiswa->program_studi,
                'nim_nisn'      => $mahasiswa->nim_nisn,
            ],
            'pendaftaran' => $pendaftaran ? [
                'id'              => $pendaftaran->id,
                'tanggal_mulai'   => $pendaftaran->tanggal_mulai,
                'tanggal_selesai' => $pendaftaran->tanggal_selesai,
                'status'          => $pendaftaran->status,
                'alasan_tolak'    => $pendaftaran->alasan_tolak,
                'pembimbing'      => $pendaftaran->pembimbing
                    ? $pendaftaran->pembimbing->user->nama_lengkap
                    : null,
                'penilaian'       => $pendaftaran->penilaian ? [
                    'kedisiplinan'     => $pendaftaran->penilaian->kedisiplinan,
                    'kemampuan_teknis' => $pendaftaran->penilaian->kemampuan_teknis,
                    'sikap'            => $pendaftaran->penilaian->sikap,
                    'kehadiran'        => $pendaftaran->penilaian->kehadiran,
                    'nilai_total'      => $pendaftaran->penilaian->nilai_total,
                    'catatan'          => $pendaftaran->penilaian->catatan,
                ] : null,
                'sertifikat'      => $pendaftaran->sertifikat ? [
                    'no_sertifikat'  => $pendaftaran->sertifikat->no_sertifikat,
                    'diterbitkan_at' => $pendaftaran->sertifikat->diterbitkan_at,
                    'tersedia'       => $pendaftaran->sertifikat->file_pdf !== null,
                ] : null,
            ] : null,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/mahasiswa/daftar
    // ----------------------------------------------------------------
    public function daftar(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        if (!$mahasiswa) {
            return response()->json(['message' => 'Profil mahasiswa tidak ditemukan.'], 404);
        }

        $existing = PendaftaranMagang::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['menunggu_persetujuan', 'disetujui', 'aktif'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Kamu sudah memiliki pendaftaran yang sedang berjalan. Selesaikan terlebih dahulu sebelum mendaftar kembali.',
            ], 422);
        }

        $request->validate([
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'file_surat'      => 'required|file|mimes:pdf,docx|max:5120',
        ]);

        $filePath = $request->file('file_surat')
            ->store('surat_pengantar', 'public');

        $pendaftaran = PendaftaranMagang::create([
            'mahasiswa_id'    => $mahasiswa->id,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'file_surat'      => $filePath,
            'status'          => 'menunggu_persetujuan',
        ]);

        return response()->json([
            'message'     => 'Pendaftaran berhasil dikirim. Silakan tunggu persetujuan dari Admin.',
            'pendaftaran' => $pendaftaran,
        ], 201);
    }

    // ----------------------------------------------------------------
    // GET /api/mahasiswa/sertifikat
    // Menampilkan sertifikat yang sudah diupload oleh Pembimbing Instansi.
    // Sistem tidak lagi membuat sertifikat secara otomatis —
    // sertifikat dibuat oleh instansi dan diupload oleh Pembimbing.
    // ----------------------------------------------------------------
    public function sertifikat(Request $request)
    {
        $mahasiswa   = $request->user()->mahasiswa;
        $pendaftaran = PendaftaranMagang::with(['penilaian', 'sertifikat', 'pembimbing.user'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'sudah_sertifikat')
            ->latest()
            ->first();

        // Belum ada pendaftaran yang selesai dinilai
        if (!$pendaftaran) {
            return response()->json([
                'tersedia' => false,
                'pesan'    => 'Sertifikat belum tersedia. Menunggu penilaian dari Pembimbing.',
            ]);
        }

        // Sudah dinilai tapi sertifikat belum diupload pembimbing
        if (!$pendaftaran->sertifikat || !$pendaftaran->sertifikat->file_pdf) {
            return response()->json([
                'tersedia' => false,
                'pesan'    => 'Magang sudah selesai dinilai. Sertifikat sedang disiapkan oleh instansi dan akan diupload oleh Pembimbing.',
            ]);
        }

        return response()->json([
            'tersedia'   => true,
            'sertifikat' => [
                'no_sertifikat'    => $pendaftaran->sertifikat->no_sertifikat,
                'diterbitkan_at'   => $pendaftaran->sertifikat->diterbitkan_at,
                'url_pdf'          => asset('storage/' . $pendaftaran->sertifikat->file_pdf),
                'nama_lengkap'     => $request->user()->nama_lengkap,
                'asal_instansi'    => $mahasiswa->asal_instansi,
                'program_studi'    => $mahasiswa->program_studi,
                'tanggal_mulai'    => $pendaftaran->tanggal_mulai,
                'tanggal_selesai'  => $pendaftaran->tanggal_selesai,
                'pembimbing'       => $pendaftaran->pembimbing?->user->nama_lengkap,
                'nilai_total'      => $pendaftaran->penilaian?->nilai_total,
                'kedisiplinan'     => $pendaftaran->penilaian?->kedisiplinan,
                'kemampuan_teknis' => $pendaftaran->penilaian?->kemampuan_teknis,
                'sikap'            => $pendaftaran->penilaian?->sikap,
                'kehadiran'        => $pendaftaran->penilaian?->kehadiran,
            ],
        ]);
    }
}