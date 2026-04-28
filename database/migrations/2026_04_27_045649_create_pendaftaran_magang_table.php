<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_magang', function (Blueprint $table) {
            $table->id();
 
            // Relasi ke mahasiswa — RESTRICT artinya data mahasiswa tidak bisa
            // dihapus jika masih ada pendaftaran yang merujuk ke dia
            $table->foreignId('mahasiswa_id')
                  ->constrained('mahasiswa')
                  ->restrictOnDelete();
 
            // Relasi ke pembimbing — nullable karena pembimbing baru diisi
            // oleh Admin saat menyetujui pendaftaran, bukan saat mahasiswa daftar.
            // nullOnDelete() artinya jika pembimbing dihapus, kolom ini jadi NULL
            // (pendaftaran tidak ikut terhapus)
            $table->foreignId('pembimbing_id')
                  ->nullable()
                  ->constrained('pembimbing')
                  ->nullOnDelete();
 
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
 
            $table->enum('status', [
                'menunggu_persetujuan',
                'disetujui',
                'ditolak',
                'aktif',
                'selesai_dinilai',
            ])->default('menunggu_persetujuan');
 
            // Hanya diisi jika status = 'ditolak'
            $table->text('alasan_tolak')->nullable();
 
            // Menyimpan path/nama file surat pengantar yang diupload,
            // bukan isi file-nya (file disimpan di storage Laravel)
            $table->string('file_surat', 255)->nullable();
 
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_magang');
    }
};
 