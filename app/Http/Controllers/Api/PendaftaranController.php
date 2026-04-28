<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\PendaftaranMagang;
use Illuminate\Http\Request;
 
class PendaftaranController extends Controller
{
    // ----------------------------------------------------------------
    // GET /api/admin/pendaftaran
    // Daftar pendaftaran masuk — bisa difilter berdasarkan status
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        $query = PendaftaranMagang::with(['mahasiswa.user', 'pembimbing.user'])
            ->latest();
 
        // Default: tampilkan yang menunggu persetujuan dulu
        if ($request->status) {
            $query->where('status', $request->status);
        }
 
        $data = $query->get()->map(function ($item) {
            return [
                'id'              => $item->id,
                'nama_lengkap'    => $item->mahasiswa->user->nama_lengkap,
                'asal_instansi'   => $item->mahasiswa->asal_instansi,
                'program_studi'   => $item->mahasiswa->program_studi,
                'tanggal_mulai'   => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'status'          => $item->status,
                'alasan_tolak'    => $item->alasan_tolak,
                'file_surat'      => $item->file_surat,
                'created_at'      => $item->created_at,
                'pembimbing'      => $item->pembimbing
                    ? $item->pembimbing->user->nama_lengkap
                    : null,
            ];
        });
 
        return response()->json($data);
    }
 
    // ----------------------------------------------------------------
    // POST /api/admin/pendaftaran/{id}/setujui
    // Admin menyetujui pendaftaran dan menugaskan pembimbing
    // ----------------------------------------------------------------
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'pembimbing_id' => 'required|exists:pembimbing,id',
        ]);
 
        $pendaftaran = PendaftaranMagang::findOrFail($id);
 
        // Pastikan hanya pendaftaran yang masih 'menunggu' yang bisa disetujui
        if ($pendaftaran->status !== 'menunggu_persetujuan') {
            return response()->json([
                'message' => 'Pendaftaran ini sudah diproses sebelumnya.',
            ], 422);
        }
 
        $pendaftaran->update([
            'status'        => 'disetujui',
            'pembimbing_id' => $request->pembimbing_id,
        ]);
 
        return response()->json(['message' => 'Pendaftaran berhasil disetujui.']);
    }
 
    // ----------------------------------------------------------------
    // POST /api/admin/pendaftaran/{id}/tolak
    // Admin menolak pendaftaran — alasan wajib diisi (sesuai UC-04)
    // ----------------------------------------------------------------
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_tolak' => 'required|string|min:10',
        ]);
 
        $pendaftaran = PendaftaranMagang::findOrFail($id);
 
        if ($pendaftaran->status !== 'menunggu_persetujuan') {
            return response()->json([
                'message' => 'Pendaftaran ini sudah diproses sebelumnya.',
            ], 422);
        }
 
        $pendaftaran->update([
            'status'       => 'ditolak',
            'alasan_tolak' => $request->alasan_tolak,
        ]);
 
        return response()->json(['message' => 'Pendaftaran berhasil ditolak.']);
    }
}
 