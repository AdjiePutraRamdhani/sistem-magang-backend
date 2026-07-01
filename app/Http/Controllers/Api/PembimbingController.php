<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\PendaftaranMagang;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\PenilaianUploadedMail;
use App\Mail\SertifikatUploadedMail;

class PembimbingController extends Controller
{
    // GET /api/pembimbing/dashboard
    public function dashboard(Request $request)
    {
        PendaftaranMagang::syncStatus();
        
        $pembimbing = $request->user()->pembimbing;

        if (!$pembimbing) {
            return response()->json(['message' => 'Profil pembimbing tidak ditemukan.'], 404);
        }

        $baseQuery = PendaftaranMagang::where('pembimbing_id', $pembimbing->id);

        return response()->json([
            'total_peserta'    => (clone $baseQuery)->count(),
            'belum_dinilai'    => (clone $baseQuery)
                                    ->whereIn('status', ['disetujui', 'aktif'])
                                    ->whereDoesntHave('penilaian')
                                    ->count(),
            'selesai_dinilai'  => (clone $baseQuery)
                                    ->where('status', 'selesai_dinilai')
                                    ->count(),
            'belum_sertifikat' => (clone $baseQuery)
                                    ->where('status', 'selesai_dinilai')
                                    ->whereDoesntHave('sertifikat')
                                    ->count(),
        ]);
    }

    // GET /api/pembimbing/peserta
    public function peserta(Request $request)
    {
        PendaftaranMagang::syncStatus();

        $pembimbing = $request->user()->pembimbing;

        $data = PendaftaranMagang::with(['mahasiswa.user', 'penilaian', 'sertifikat'])
            ->where('pembimbing_id', $pembimbing->id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id'              => $item->id,
                    'nama_lengkap'    => $item->mahasiswa->user->nama_lengkap,
                    'asal_instansi'   => $item->mahasiswa->asal_instansi,
                    'program_studi'   => $item->mahasiswa->program_studi,
                    'tanggal_mulai'   => $item->tanggal_mulai->format('d M Y'),
                    'tanggal_selesai' => $item->tanggal_selesai->format('d M Y'),
                    'status'          => $item->status,
                    'sudah_dinilai'   => $item->penilaian !== null,
                    'sudah_sertifikat'=> $item->sertifikat !== null,
                    'penilaian'       => $item->penilaian ? [
                        'nilai_total'      => $item->penilaian->nilai_total,
                        'kedisiplinan'     => $item->penilaian->kedisiplinan,
                        'kemampuan_teknis' => $item->penilaian->kemampuan_teknis,
                        'sikap'            => $item->penilaian->sikap,
                        'kehadiran'        => $item->penilaian->kehadiran,
                        'catatan'          => $item->penilaian->catatan,
                    ] : null,
                    'sertifikat'      => $item->sertifikat ? [
                        'no_sertifikat'  => $item->sertifikat->no_sertifikat,
                        'diterbitkan_at' => $item->sertifikat->diterbitkan_at,
                        'file_pdf'       => $item->sertifikat->file_pdf
                            ? asset('storage/' . $item->sertifikat->file_pdf)
                            : null,
                    ] : null,
                ];
            });

        return response()->json($data);
    }

    // POST /api/pembimbing/nilai/{id}
    public function simpanNilai(Request $request, $id)
    {
        PendaftaranMagang::syncStatus();
        $pembimbing  = $request->user()->pembimbing;

        $pendaftaran = PendaftaranMagang::where('id', $id)
            ->where('pembimbing_id', $pembimbing->id)
            ->firstOrFail();

        if ($pendaftaran->tanggal_selesai > today()) {
            return response()->json([
                'message' => 'Penilaian belum bisa diberikan karena periode magang belum selesai.',
            ], 422);
        }

        if ($pendaftaran->penilaian) {
            return response()->json([
                'message' => 'Peserta ini sudah pernah dinilai sebelumnya.',
            ], 422);
        }

        $request->validate([
            'kedisiplinan'     => 'required|integer|between:0,100',
            'kemampuan_teknis' => 'required|integer|between:0,100',
            'sikap'            => 'required|integer|between:0,100',
            'kehadiran'        => 'required|integer|between:0,100',
            'catatan'          => 'nullable|string|max:1000',
        ]);

        $nilaiTotal = (int) round(
            ($request->kedisiplinan + $request->kemampuan_teknis
             + $request->sikap + $request->kehadiran) / 4
        );

        Penilaian::create([
            'pendaftaran_id'   => $pendaftaran->id,
            'kedisiplinan'     => $request->kedisiplinan,
            'kemampuan_teknis' => $request->kemampuan_teknis,
            'sikap'            => $request->sikap,
            'kehadiran'        => $request->kehadiran,
            'nilai_total'      => $nilaiTotal,
            'catatan'          => $request->catatan,
        ]);

        $pendaftaran->update(['status' => 'selesai_dinilai']);

        $pendaftaran->load(['mahasiswa.user', 'penilaian', 'pembimbing.user']);

        // Kirim email ke mahasiswa (nilai telah selesai diinput)
        try {
            Mail::to($pendaftaran->mahasiswa->user->email)->send(new PenilaianUploadedMail($pendaftaran));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email penilaian selesai ke mahasiswa: ' . $e->getMessage());
        }

        return response()->json([
            'message'     => 'Penilaian berhasil disimpan.',
            'nilai_total' => $nilaiTotal,
        ]);
    }

    // POST /api/pembimbing/sertifikat/{id}
    // Pembimbing mengupload file PDF sertifikat yang sudah dibuat oleh instansi
    public function uploadSertifikat(Request $request, $id)
    {
        PendaftaranMagang::syncStatus();
        $pembimbing = $request->user()->pembimbing;

        $pendaftaran = PendaftaranMagang::with(['mahasiswa.user'])
            ->where('id', $id)
            ->where('pembimbing_id', $pembimbing->id)
            ->firstOrFail();

        // Sertifikat hanya bisa diupload setelah peserta dinilai
        if ($pendaftaran->status !== 'selesai_dinilai') {
            return response()->json([
                'message' => 'Sertifikat hanya bisa diupload setelah peserta selesai dinilai.',
            ], 422);
        }

        $request->validate([
            'file_pdf'      => 'required|file|mimes:pdf|max:5120', // maks 5MB
            'no_sertifikat' => 'required|string|max:50',
        ]);

        // Hapus file lama jika ada (untuk kasus re-upload / koreksi)
        if ($pendaftaran->sertifikat && $pendaftaran->sertifikat->file_pdf) {
            Storage::disk('public')->delete($pendaftaran->sertifikat->file_pdf);
            $pendaftaran->sertifikat->delete();
        }

        // Simpan file PDF ke storage/app/public/sertifikat/
        $filePath = $request->file('file_pdf')->store('sertifikat', 'public');

        Sertifikat::create([
            'pendaftaran_id' => $pendaftaran->id,
            'no_sertifikat'  => $request->no_sertifikat,
            'file_pdf'       => $filePath,
        ]);

        $pendaftaran->update(['status' => 'sudah_sertifikat']);

        $pendaftaran->load(['mahasiswa.user', 'sertifikat']);

        // Kirim email ke mahasiswa (sertifikat tersedia)
        try {
            Mail::to($pendaftaran->mahasiswa->user->email)->send(new SertifikatUploadedMail($pendaftaran));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email sertifikat tersedia ke mahasiswa: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Sertifikat berhasil diupload.',
        ], 201);
    }
}