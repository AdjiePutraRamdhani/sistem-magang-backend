<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Pembimbing extends Model
{
    protected $table = 'pembimbing';
    public $timestamps = false;
 
    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
        'bidang',
    ];
 
    // --- RELASI ---
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function pendaftaranMagang()
    {
        return $this->hasMany(PendaftaranMagang::class);
    }
}
 