<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    public $timestamps = false;

    // Sama seperti perbaikan yang kamu lakukan sebelumnya —
    // Laravel butuh tahu nama tabel yang sebenarnya
    protected $table = 'sertifikat';

    // Daftarkan semua kolom yang boleh diisi via create() atau fill()
    protected $fillable = [
        'pendaftaran_id',
        'no_sertifikat',
        'file_pdf',
    ];

    // Relasi balik ke pendaftaran
    public function pendaftaranMagang()
    {
        return $this->belongsTo(PendaftaranMagang::class, 'pendaftaran_id');
    }
}