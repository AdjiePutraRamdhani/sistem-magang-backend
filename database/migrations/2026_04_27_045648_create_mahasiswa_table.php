<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            // foreignId() adalah cara Laravel membuat kolom INT UNSIGNED
            // dan sekaligus mendaftarkan foreign key secara otomatis
            $table->foreignId('user_id')
                  ->unique()              // 1 user hanya boleh punya 1 profil mahasiswa
                  ->constrained('users')  // merujuk ke tabel users
                  ->cascadeOnDelete();    // jika user dihapus, data mahasiswa ikut terhapus
            $table->string('nim_nisn', 30)->nullable();
            $table->string('asal_instansi', 200);
            $table->string('program_studi', 150)->nullable();
            // Tidak ada timestamps di sini karena data profil
            // hanya dibuat sekali dan jarang berubah strukturnya
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
 