<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
 
class PendaftaranMagang extends Model
{
    protected $table = 'pendaftaran_magang';
 
    protected $fillable = [
        'mahasiswa_id',
        'pembimbing_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'alasan_tolak',
        'file_surat',
    ];
 
    // $casts memberitahu Laravel untuk otomatis mengubah tipe data
    // kolom tertentu. Misalnya kolom tanggal akan otomatis
    // diubah menjadi objek Carbon (library tanggal Laravel),
    // bukan string biasa — lebih mudah untuk manipulasi tanggal.
    protected $casts = [
        'tanggal_mulai'   => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];

    public static function syncStatus()
    {
        $today = Carbon::today();

        // Disetujui -> Aktif
        self::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->update([
                'status' => 'aktif'
            ]);
    }
 
    // --- RELASI ---
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
 
    // nullable() di relasi ini karena pembimbing_id bisa NULL
    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class);
    }
 
    // hasOne karena satu pendaftaran hanya memiliki satu penilaian
    public function penilaian()
    {
        return $this->hasOne(Penilaian::class, 'pendaftaran_id');
    }
 
    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class, 'pendaftaran_id');
    }
}
 