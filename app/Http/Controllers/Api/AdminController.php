<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Pembimbing;
use App\Models\PendaftaranMagang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomePembimbingMail;
 
class AdminController extends Controller
{
    // ----------------------------------------------------------------
    // GET /api/admin/dashboard
    // Mengembalikan data statistik ringkasan untuk halaman dashboard
    // ----------------------------------------------------------------
    public function dashboard()
    {
        PendaftaranMagang::syncStatus();

        return response()->json([
            'total_peserta'   => PendaftaranMagang::whereIn('status', ['disetujui','aktif','selesai_dinilai'])->count(),
            'menunggu'        => PendaftaranMagang::where('status', 'menunggu_persetujuan')->count(),
            'aktif'           => PendaftaranMagang::where('status', 'aktif')->count(),
            'selesai_dinilai' => PendaftaranMagang::where('status', 'selesai_dinilai')->count(),
            'sudah_sertifikat' => PendaftaranMagang::where('status', 'sudah_sertifikat')->count(),
        ]);
    }
 
    // ----------------------------------------------------------------
    // GET /api/admin/mahasiswa
    // Mengembalikan daftar semua peserta magang beserta relasi user-nya
    // ----------------------------------------------------------------
    public function indexMahasiswa(Request $request)
    {
        PendaftaranMagang::syncStatus();

        $query = PendaftaranMagang::with([
            'mahasiswa.user',
            'pembimbing.user',
        ]);
 
        if ($request->search) {
            $query->whereHas('mahasiswa.user', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', "%{$request->search}%");
            })->orWhereHas('mahasiswa', function ($q) use ($request) {
                $q->where('asal_instansi', 'like', "%{$request->search}%");
            });
        }
 
        if ($request->status) {
            $query->where('status', $request->status);
        }
 
        $data = $query->latest()->get()->map(function ($item) {
            return [
                'id'              => $item->id,
                'nama_lengkap'    => $item->mahasiswa->user->nama_lengkap,
                'asal_instansi'   => $item->mahasiswa->asal_instansi,
                'program_studi'   => $item->mahasiswa->program_studi,
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
    
    // ----------------------------------------------------------------
    // DELETE /api/admin/mahasiswa/{id}
    // ----------------------------------------------------------------
    public function destroyMahasiswa($id)
    {
        $pendaftaran = PendaftaranMagang::findOrFail($id);
 
        $pendaftaran->delete();
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
 
    // ----------------------------------------------------------------
    // GET /api/admin/pembimbing
    // ----------------------------------------------------------------
    public function indexPembimbing()
    {
        $data = Pembimbing::with('user')->withCount('pendaftaranMagang')->get()->map(fn($p) => [
            'id'           => $p->id,
            'nama_lengkap' => $p->user->nama_lengkap,
            'email'        => $p->user->email,
            'nip'          => $p->nip,
            'no_telepon'   => $p->user->no_telepon,
            'jabatan'      => $p->jabatan,
            'bidang'       => $p->bidang,
            'total_mhs'    => $p->pendaftaran_magang_count,
        ]);
 
        return response()->json($data);
    }
 
    // ----------------------------------------------------------------
    // POST /api/admin/pembimbing
    // ----------------------------------------------------------------
    public function storePembimbing(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8',
            'nip'          => 'nullable|string|max:30',
            'no_telepon'   => 'required|string|max:20',
            'jabatan'      => 'nullable|string|max:150',
            'bidang'       => 'nullable|string|max:150',
        ]);
 
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'no_telepon'   => $request->no_telepon,
            'role'         => 'pembimbing',
        ]);
 
        Pembimbing::create([
            'user_id' => $user->id,
            'nip'     => $request->nip,
            'jabatan' => $request->jabatan,
            'bidang'  => $request->bidang,
        ]);

        // Kirim email selamat datang ke pembimbing
        try {
            Mail::to($user->email)->send(new WelcomePembimbingMail($user, $request->password));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email welcome pembimbing: ' . $e->getMessage());
        }
 
        return response()->json(['message' => 'Akun pembimbing berhasil dibuat.'], 201);
    }

    // ----------------------------------------------------------------
    // DELETE /api/admin/pembimbing/{id}
    // ----------------------------------------------------------------
    public function destroyPembimbing($id)
    {
        $pembimbing = Pembimbing::findOrFail($id);
        $user = $pembimbing->user;
        
        $pembimbing->delete();
        
        if ($user) {
            $user->delete();
        }

        return response()->json(['message' => 'Data pembimbing berhasil dihapus.']);
    }
}
 