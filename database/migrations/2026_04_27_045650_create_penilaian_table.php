<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
 
            // unique() memastikan 1 pendaftaran hanya bisa dinilai 1 kali
            $table->foreignId('pendaftaran_id')
                  ->unique()
                  ->constrained('pendaftaran_magang')
                  ->cascadeOnDelete(); // jika pendaftaran dihapus, nilai ikut terhapus
 
            // unsignedTinyInteger() = TINYINT UNSIGNED (0–255),
            // cukup untuk menyimpan nilai 0–100 dan lebih hemat dibanding INT
            $table->unsignedTinyInteger('kedisiplinan')->default(0);
            $table->unsignedTinyInteger('kemampuan_teknis')->default(0);
            $table->unsignedTinyInteger('sikap')->default(0);
            $table->unsignedTinyInteger('kehadiran')->default(0);
 
            // nilai_total akan dihitung otomatis di level aplikasi (bukan trigger)
            // saat Pembimbing menyimpan penilaian — lebih mudah di-maintain di Laravel
            $table->unsignedTinyInteger('nilai_total')->default(0);
 
            $table->text('catatan')->nullable();
 
            // Menyimpan waktu kapan penilaian diberikan
            $table->timestamp('dinilai_at')->useCurrent();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
 