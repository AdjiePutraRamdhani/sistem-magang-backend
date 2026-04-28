<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Mahasiswa extends Model
{
    // Karena tabel ini tidak punya kolom created_at & updated_at,
    // kita perlu memberitahu Laravel untuk tidak mencarinya
    protected $table = 'mahasiswa';
    public $timestamps = false;
 
    protected $fillable = [
        'user_id',
        'nim_nisn',
        'asal_instansi',
        'program_studi',
    ];
 
    // --- RELASI ---
    // belongsTo adalah kebalikan dari hasOne.
    // "Mahasiswa ini dimiliki oleh satu User."
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    // hasMany berarti "satu Mahasiswa bisa punya banyak pendaftaran".
    // (Secara logika, idealnya hanya satu yang aktif pada satu waktu,
    // tapi secara data bisa lebih dari satu — misal mendaftar lagi tahun depan)
    public function pendaftaranMagang()
    {
        return $this->hasMany(PendaftaranMagang::class);
    }
}
 