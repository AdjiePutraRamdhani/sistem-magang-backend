<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
 
class AuthController extends Controller
{
    // ----------------------------------------------------------------
    // REGISTER — Hanya untuk Mahasiswa/Siswa Magang
    // Endpoint: POST /api/register
    // ----------------------------------------------------------------
    public function register(Request $request)
    {
        // Validasi input. Jika ada yang tidak sesuai aturan,
        // Laravel otomatis mengembalikan response 422 dengan pesan error.
        $request->validate([
            'nama_lengkap'  => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email', // email tidak boleh duplikat
            'password'      => 'required|string|min:8|confirmed',   // confirmed = harus ada field password_confirmation
            'no_telepon'    => 'nullable|string|max:20',
            'nim_nisn'      => 'nullable|string|max:30',
            'asal_instansi' => 'required|string|max:200',
            'program_studi' => 'nullable|string|max:150',
        ]);
 
        // Buat akun user baru di tabel users.
        // Hash::make() mengenkripsi password — WAJIB dilakukan,
        // jangan pernah simpan password dalam bentuk teks biasa!
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'no_telepon'   => $request->no_telepon,
            'role'         => 'mahasiswa', // role selalu 'mahasiswa' saat register mandiri
        ]);
 
        // Setelah user dibuat, langsung buat juga profil mahasiswanya
        // di tabel mahasiswa. Ingat: user dan mahasiswa adalah 2 tabel terpisah.
        Mahasiswa::create([
            'user_id'       => $user->id,
            'nim_nisn'      => $request->nim_nisn,
            'asal_instansi' => $request->asal_instansi,
            'program_studi' => $request->program_studi,
        ]);
 
        // Buat token untuk user yang baru dibuat agar bisa langsung login
        $token = $user->createToken('auth_token')->plainTextToken;
 
        // Kembalikan response JSON dengan status 201 (Created)
        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token'   => $token,
            'user'    => [
                'id'           => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'email'        => $user->email,
                'role'         => $user->role,
            ],
        ], 201);
    }
 
    // ----------------------------------------------------------------
    // LOGIN — Untuk semua aktor (Admin, Mahasiswa, Pembimbing)
    // Endpoint: POST /api/login
    // ----------------------------------------------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
 
        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();
 
        // Cek apakah user ditemukan DAN passwordnya cocok.
        // Hash::check() membandingkan password teks biasa dengan hash di database.
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Melempar exception validasi — Laravel otomatis mengembalikan
            // response 422 dengan pesan error yang sesuai
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }
 
        // Hapus semua token lama milik user ini (opsional tapi disarankan
        // agar tidak ada token ganda yang menumpuk di database)
        $user->tokens()->delete();
 
        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;
 
        // Load relasi mahasiswa atau pembimbing tergantung role-nya,
        // agar frontend bisa langsung menggunakan data profil tanpa
        // harus melakukan request tambahan
        $profile = null;
        if ($user->role === 'mahasiswa') {
            $profile = $user->mahasiswa;
        } elseif ($user->role === 'pembimbing') {
            $profile = $user->pembimbing;
        }
 
        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'           => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'email'        => $user->email,
                'role'         => $user->role,
                'profile'      => $profile,
            ],
        ]);
    }
 
    // ----------------------------------------------------------------
    // LOGOUT
    // Endpoint: POST /api/logout
    // Route ini dilindungi middleware 'auth:sanctum', artinya
    // hanya bisa diakses jika request menyertakan token yang valid.
    // ----------------------------------------------------------------
    public function logout(Request $request)
    {
        // Hapus HANYA token yang sedang dipakai saat ini
        // (bukan semua token user — berguna jika user login dari banyak perangkat)
        $request->user()->currentAccessToken()->delete();
 
        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
 
    // ----------------------------------------------------------------
    // ME — Mengambil data user yang sedang login
    // Endpoint: GET /api/me
    // Berguna bagi frontend untuk mengecek status login saat halaman dimuat ulang
    // ----------------------------------------------------------------
    public function me(Request $request)
    {
        $user = $request->user();
 
        $profile = null;
        if ($user->role === 'mahasiswa') {
            $profile = $user->mahasiswa;
        } elseif ($user->role === 'pembimbing') {
            $profile = $user->pembimbing;
        }
 
        return response()->json([
            'user' => [
                'id'           => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'email'        => $user->email,
                'role'         => $user->role,
                'profile'      => $profile,
            ],
        ]);
    }
}
 