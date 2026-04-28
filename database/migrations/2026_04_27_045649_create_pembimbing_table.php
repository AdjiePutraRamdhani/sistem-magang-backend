<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('nip', 30)->nullable();      // Nomor Induk Pegawai
            $table->string('jabatan', 150)->nullable();
            $table->string('bidang', 150)->nullable();  // bidang/divisi di instansi
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('pembimbing');
    }
};
 