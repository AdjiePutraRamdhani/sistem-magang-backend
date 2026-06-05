<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pendaftaran_magang
            MODIFY COLUMN status ENUM(
                'menunggu',
                'disetujui',
                'aktif',
                'selesai_dinilai',
                'sudah_sertifikat'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE pendaftaran_magang
            MODIFY COLUMN status ENUM(
                'menunggu',
                'disetujui',
                'aktif',
                'selesai_dinilai'
            ) NOT NULL
        ");
    }
};