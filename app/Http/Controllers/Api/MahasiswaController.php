<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranMagang;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    // ----------------------------------------------------------------
    // GET /api/mahasiswa/dashboard
    // Mengembalikan data pendaftaran aktif milik mahasiswa yang login
    // ----------------------------------------------------------------
    public function dashboard(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        if (!$mahasiswa) {
            return response()->json(['message' => 'Profil mahasiswa tidak ditemukan.'], 404);
        }

        // Ambil pendaftaran terbaru milik mahasiswa ini beserta relasi
        // penilaian dan sertifikat jika sudah ada
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
                    'no_sertifikat' => $pendaftaran->sertifikat->no_sertifikat,
                    'diterbitkan_at'=> $pendaftaran->sertifikat->diterbitkan_at,
                ] : null,
            ] : null,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/mahasiswa/daftar
    // Mahasiswa mengajukan pendaftaran magang — sesuai UC-03
    // ----------------------------------------------------------------
    public function daftar(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        if (!$mahasiswa) {
            return response()->json(['message' => 'Profil mahasiswa tidak ditemukan.'], 404);
        }

        // Cek apakah sudah ada pendaftaran yang sedang aktif atau menunggu.
        // Mahasiswa tidak boleh mendaftar dua kali bersamaan.
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
            // File surat pengantar: wajib, format PDF atau DOCX, maks 5MB
            'file_surat'      => 'required|file|mimes:pdf,docx|max:5120',
        ]);

        // Simpan file surat ke storage Laravel.
        // Storage::disk('public') menyimpan file di folder 'storage/app/public/'
        // yang bisa diakses publik setelah menjalankan 'php artisan storage:link'
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
    // Mengecek ketersediaan sertifikat dan mengembalikan data lengkapnya
    // ----------------------------------------------------------------
    public function sertifikat(Request $request)
    {
        $mahasiswa   = $request->user()->mahasiswa;
        $pendaftaran = PendaftaranMagang::with(['penilaian', 'sertifikat', 'pembimbing.user'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'selesai_dinilai')
            ->latest()
            ->first();

        if (!$pendaftaran) {
            return response()->json([
                'tersedia' => false,
                'message'  => 'Sertifikat belum tersedia. Menunggu penilaian dari Pembimbing.',
            ]);
        }

        // Jika sertifikat belum pernah diterbitkan, buat sekarang
        if (!$pendaftaran->sertifikat) {
            $noSertifikat = $this->generateNomorSertifikat();

            $sertifikat = Sertifikat::create([
                'pendaftaran_id' => $pendaftaran->id,
                'no_sertifikat'  => $noSertifikat,
            ]);

            $pendaftaran->load('sertifikat');
        }

        return response()->json([
            'tersedia'    => true,
            'sertifikat'  => [
                'no_sertifikat'    => $pendaftaran->sertifikat->no_sertifikat,
                'diterbitkan_at'   => $pendaftaran->sertifikat->diterbitkan_at,
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

    // ----------------------------------------------------------------
    // Fungsi pembantu: generate nomor sertifikat unik
    // Format: DISPUSIP/MAG/YYYY/XXXX (contoh: DISPUSIP/MAG/2025/0042)
    // ----------------------------------------------------------------
    private function generateNomorSertifikat(): string
    {
        $tahun  = date('Y');
        $urutan = Sertifikat::whereYear('diterbitkan_at', $tahun)->count() + 1;
        return sprintf('DISPUSIP/MAG/%s/%04d', $tahun, $urutan);
    }
}