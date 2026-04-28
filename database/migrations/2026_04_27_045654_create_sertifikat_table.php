<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
 
            // unique() memastikan 1 pendaftaran hanya menghasilkan 1 sertifikat
            $table->foreignId('pendaftaran_id')
                  ->unique()
                  ->constrained('pendaftaran_magang')
                  ->cascadeOnDelete();
 
            // Nomor sertifikat unik, format: DISPUSIP/MAG/YYYY/XXXX
            $table->string('no_sertifikat', 50)->unique();
 
            // Path file PDF sertifikat di storage Laravel
            $table->string('file_pdf', 255)->nullable();
 
            // Waktu sertifikat diterbitkan
            $table->timestamp('diterbitkan_at')->useCurrent();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
 