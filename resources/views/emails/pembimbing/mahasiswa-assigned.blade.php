@extends('emails.layout')

@section('title', 'Tugas Bimbingan Peserta Magang Baru')

@section('content')
<h2 style="margin-top: 0; color: #1e3a8a; font-size: 20px; font-weight: 600;">Halo, Bapak/Ibu {{ $pendaftaran->pembimbing->user->nama_lengkap }}!</h2>
<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Anda telah ditugaskan oleh Administrator untuk menjadi <strong>Pembimbing Instansi</strong> bagi peserta magang berikut:
</p>

<div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <h3 style="margin-top: 0; color: #1e3a8a; font-size: 16px; font-weight: 600; margin-bottom: 12px;">Profil Peserta Magang:</h3>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280; width: 140px;">Nama Peserta:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->mahasiswa->user->nama_lengkap }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Asal Instansi:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->mahasiswa->asal_instansi }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Program Studi:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->mahasiswa->program_studi }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Jadwal Pelaksanaan:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->tanggal_mulai->format('d M Y') }} s/d {{ $pendaftaran->tanggal_selesai->format('d M Y') }}</td>
        </tr>
    </table>
</div>

<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Silakan pantau perkembangan peserta magang Anda dan lakukan penilaian berkala atau pengunggahan sertifikat magang setelah periode magang mereka berakhir.
</p>

<div style="text-align: center; margin: 32px 0 16px 0;">
    <a href="{{ config('app.url') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px 0 rgba(37, 99, 235, 0.2);">
        Kelola Bimbingan
    </a>
</div>
@endsection
