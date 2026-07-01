@extends('emails.layout')

@section('title', 'Registrasi Akun Berhasil')

@section('content')
<h2 style="margin-top: 0; color: #1e3a8a; font-size: 20px; font-weight: 600;">Halo, {{ $user->nama_lengkap }}!</h2>
<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Selamat, registrasi akun Anda di <strong>Sistem Informasi Magang Dinas Perpustakaan dan Kearsipan Provinsi Riau</strong> telah berhasil.
</p>

<div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <h3 style="margin-top: 0; color: #1e3a8a; font-size: 16px; font-weight: 600; margin-bottom: 12px;">Detail Akun Anda:</h3>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280; width: 120px;">Nama Lengkap:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $user->nama_lengkap }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Email:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">No. Telepon:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $user->no_telepon }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Role:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;"><span style="background-color: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 9999px; font-size: 12px; text-transform: uppercase;">{{ $user->role }}</span></td>
        </tr>
    </table>
</div>

<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Langkah selanjutnya, silakan login ke aplikasi menggunakan email dan password Anda untuk mengajukan pendaftaran magang secara resmi dengan mengunggah Surat Pengantar Instansi.
</p>

<div style="text-align: center; margin: 32px 0 16px 0;">
    <a href="{{ config('app.url') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px 0 rgba(37, 99, 235, 0.2);">
        Masuk Ke Dashboard
    </a>
</div>
@endsection
