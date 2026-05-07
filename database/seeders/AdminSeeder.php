<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
 
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'nama_lengkap' => 'Administrator',
            'email'        => 'admin@dispusip-riau.go.id',
            // Hash::make() mengenkripsi password menggunakan bcrypt
            // JANGAN simpan password dalam bentuk teks biasa di database!
            'password'     => Hash::make('admin123'),
            'role'         => 'admin',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
 