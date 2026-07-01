<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Pembimbing;
use App\Models\PendaftaranMagang;
use App\Models\Penilaian;
use App\Models\Sertifikat;
use App\Mail\WelcomeMahasiswaMail;
use App\Mail\WelcomePembimbingMail;
use App\Mail\PendaftaranStatusMail;
use App\Mail\MahasiswaAssignedMail;
use App\Mail\PenilaianUploadedMail;
use App\Mail\SertifikatUploadedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_mahasiswa_registration_sends_email()
    {
        Mail::fake();

        $response = $this->postJson('/api/register', [
            'nama_lengkap'          => 'Mahasiswa Test',
            'email'                 => 'mahasiswa.test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'no_telepon'            => '081234567890',
            'nim_nisn'              => '1234567890',
            'asal_instansi'         => 'Universitas Test',
            'program_studi'         => 'Teknik Informatika',
        ]);

        $response->assertStatus(201);

        Mail::assertSent(WelcomeMahasiswaMail::class, function ($mail) {
            return $mail->hasTo('mahasiswa.test@example.com');
        });
    }

    public function test_admin_store_pembimbing_sends_email()
    {
        Mail::fake();

        $admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email'        => 'admin.test@example.com',
            'password'     => bcrypt('password'),
            'no_telepon'   => '0812345678',
            'role'         => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/api/admin/pembimbing', [
                'nama_lengkap' => 'Pembimbing Test',
                'email'        => 'pembimbing.test@example.com',
                'password'     => 'password123',
                'nip'          => '1987654321',
                'no_telepon'   => '0812345678',
                'jabatan'      => 'Staff',
                'bidang'       => 'IT',
            ]);

        $response->assertStatus(201);

        Mail::assertSent(WelcomePembimbingMail::class, function ($mail) {
            return $mail->hasTo('pembimbing.test@example.com') && $mail->password === 'password123';
        });
    }

    public function test_pendaftaran_approval_and_rejection_sends_emails()
    {
        Mail::fake();

        $admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email'        => 'admin.test@example.com',
            'password'     => bcrypt('password'),
            'no_telepon'   => '0812345678',
            'role'         => 'admin',
        ]);

        $userMhs = User::create([
            'nama_lengkap' => 'Mahasiswa Test',
            'email'        => 'mhs@example.com',
            'password'     => bcrypt('password'),
            'no_telepon'   => '0812345678',
            'role'         => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id'       => $userMhs->id,
            'nim_nisn'      => '12345',
            'asal_instansi' => 'ITS',
            'program_studi' => 'IF',
        ]);

        $userPem = User::create([
            'nama_lengkap' => 'Pembimbing Test',
            'email'        => 'pem@example.com',
            'password'     => bcrypt('password'),
            'no_telepon'   => '0812345678',
            'role'         => 'pembimbing',
        ]);

        $pem = Pembimbing::create([
            'user_id' => $userPem->id,
            'nip'     => '123',
        ]);

        // Pendaftaran 1 untuk disetujui
        $pendaftaran1 = PendaftaranMagang::create([
            'mahasiswa_id'    => $mhs->id,
            'tanggal_mulai'   => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(30),
            'file_surat'      => 'surat.pdf',
            'status'          => 'menunggu_persetujuan',
        ]);

        $response1 = $this->actingAs($admin)
            ->postJson("/api/admin/pendaftaran/{$pendaftaran1->id}/setujui", [
                'pembimbing_id' => $pem->id,
            ]);

        $response1->assertStatus(200);

        Mail::assertSent(PendaftaranStatusMail::class, function ($mail) {
            return $mail->hasTo('mhs@example.com') && $mail->pendaftaran->status === 'disetujui';
        });

        Mail::assertSent(MahasiswaAssignedMail::class, function ($mail) {
            return $mail->hasTo('pem@example.com');
        });

        // Pendaftaran 2 untuk ditolak
        $pendaftaran2 = PendaftaranMagang::create([
            'mahasiswa_id'    => $mhs->id,
            'tanggal_mulai'   => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(30),
            'file_surat'      => 'surat.pdf',
            'status'          => 'menunggu_persetujuan',
        ]);

        $response2 = $this->actingAs($admin)
            ->postJson("/api/admin/pendaftaran/{$pendaftaran2->id}/tolak", [
                'alasan_tolak' => 'Surat pengantar tidak lengkap dan tidak jelas.',
            ]);

        $response2->assertStatus(200);

        Mail::assertSent(PendaftaranStatusMail::class, function ($mail) {
            return $mail->hasTo('mhs@example.com') && $mail->pendaftaran->status === 'ditolak';
        });
    }

    public function test_pembimbing_grading_and_certificate_sends_emails()
    {
        Mail::fake();

        $userMhs = User::create([
            'nama_lengkap' => 'Mahasiswa Test',
            'email'        => 'mhs@example.com',
            'password'     => bcrypt('password'),
            'no_telepon'   => '0812345678',
            'role'         => 'mahasiswa',
        ]);

        $mhs = Mahasiswa::create([
            'user_id'       => $userMhs->id,
            'nim_nisn'      => '12345',
            'asal_instansi' => 'ITS',
            'program_studi' => 'IF',
        ]);

        $userPem = User::create([
            'nama_lengkap' => 'Pembimbing Test',
            'email'        => 'pem@example.com',
            'password'     => bcrypt('password'),
            'no_telepon'   => '0812345678',
            'role'         => 'pembimbing',
        ]);

        $pem = Pembimbing::create([
            'user_id' => $userPem->id,
            'nip'     => '123',
        ]);

        // Pendaftaran aktif yang sudah lewat tanggal selesainya untuk dinilai
        $pendaftaran = PendaftaranMagang::create([
            'mahasiswa_id'    => $mhs->id,
            'pembimbing_id'   => $pem->id,
            'tanggal_mulai'   => now()->subDays(30),
            'tanggal_selesai' => now()->subDays(1), // sudah selesai kemarin
            'file_surat'      => 'surat.pdf',
            'status'          => 'aktif',
        ]);

        // Simpan nilai
        $response1 = $this->actingAs($userPem)
            ->postJson("/api/pembimbing/nilai/{$pendaftaran->id}", [
                'kedisiplinan'     => 90,
                'kemampuan_teknis' => 85,
                'sikap'            => 95,
                'kehadiran'        => 100,
                'catatan'          => 'Sangat baik sekali kinerjanya.',
            ]);

        $response1->assertStatus(200);

        Mail::assertSent(PenilaianUploadedMail::class, function ($mail) {
            return $mail->hasTo('mhs@example.com') && $mail->pendaftaran->penilaian->nilai_total === 93;
        });

        // Upload sertifikat
        $file = UploadedFile::fake()->create('sertifikat.pdf', 1000, 'application/pdf');

        $response2 = $this->actingAs($userPem)
            ->postJson("/api/pembimbing/sertifikat/{$pendaftaran->id}", [
                'file_pdf'      => $file,
                'no_sertifikat' => 'CERT-12345',
            ]);

        $response2->assertStatus(201);

        Mail::assertSent(SertifikatUploadedMail::class, function ($mail) {
            return $mail->hasTo('mhs@example.com');
        });
    }
}
