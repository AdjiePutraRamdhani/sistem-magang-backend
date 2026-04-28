<?php
 
namespace App\Models;
 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
 
// Authenticatable adalah kelas khusus Laravel untuk model yang bisa login.
// HasApiTokens adalah trait dari Sanctum yang menambahkan kemampuan
// membuat dan mengelola token autentikasi.
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
 
    // $fillable mendefinisikan kolom mana yang boleh diisi secara massal
    // (misalnya saat User::create([...])). Ini perlindungan keamanan
    // agar hacker tidak bisa mengisi kolom yang tidak kita inginkan.
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'no_telepon',
        'role',
    ];
 
    // $hidden mendefinisikan kolom yang TIDAK akan tampil saat data
    // diubah ke JSON. Password tentu tidak boleh dikirim ke frontend!
    protected $hidden = [
        'password',
    ];
 
    // --- RELASI ---
    // hasOne berarti "satu User memiliki satu Mahasiswa".
    // Laravel akan mencari baris di tabel mahasiswa yang user_id-nya
    // sama dengan id User ini.
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }
 
    public function pembimbing()
    {
        return $this->hasOne(Pembimbing::class);
    }
}
 