<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                  // INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('nama_lengkap', 150);
            $table->string('email', 150)->unique();        // email harus unik, dipakai untuk login
            $table->string('password');                    // akan disimpan dalam bentuk hash bcrypt
            $table->string('no_telepon', 20)->nullable();  // nullable = boleh kosong
            $table->enum('role', ['admin', 'mahasiswa', 'pembimbing'])->default('mahasiswa');
            $table->timestamps();                          // otomatis buat kolom created_at & updated_at
        });
    }
 
    public function down(): void
    {
        // down() dipanggil saat migration di-rollback (dibatalkan)
        Schema::dropIfExists('users');
    }
};
 