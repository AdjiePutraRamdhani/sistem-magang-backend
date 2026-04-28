<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Penilaian extends Model
{
    public $timestamps = false;
 
    protected $table = 'penilaian';
 
    protected $fillable = [
        'pendaftaran_id',
        'kedisiplinan',
        'kemampuan_teknis',
        'sikap',
        'kehadiran',
        'nilai_total',
        'catatan',
    ];
 
    // --- RELASI ---
    public function pendaftaranMagang()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'pendaftaran_id');
    }
}
 






