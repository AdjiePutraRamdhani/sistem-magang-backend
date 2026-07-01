@extends('emails.layout')

@section('title', 'Akun Pembimbing Magang Dibuat')

@section('content')
<h2 style="margin-top: 0; color: #1e3a8a; font-size: 20px; font-weight: 600;">Halo, Bapak/Ibu {{ $user->nama_lengkap }}!</h2>
<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Akun Anda sebagai <strong>Pembimbing Instansi</strong> di Sistem Informasi Magang Dinas Perpustakaan dan Kearsipan Provinsi Riau telah berhasil dibuat oleh Administrator.
</p>

<div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <h3 style="margin-top: 0; color: #1e3a8a; font-size: 16px; font-weight: 600; margin-bottom: 12px;">Kredensial Login Anda:</h3>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280; width: 120px;">Email:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Password:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600; font-family: monospace; font-size: 15px; letter-spacing: 0.5px;">{{ $password }}</td>
        </tr>
    </table>
    <div style="margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 12px; font-size: 13px; color: #ef4444; font-weight: 500;">
        *Demi keamanan, disarankan untuk segera mengganti password setelah Anda login pertama kali.
    </div>
</div>

<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Silakan gunakan kredensial di atas untuk masuk ke dashboard pembimbing dan mengelola mahasiswa magang yang ditugaskan kepada Anda.
</p>

<div style="text-align: center; margin: 32px 0 16px 0;">
    <a href="{{ config('app.url') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px 0 rgba(37, 99, 235, 0.2);">
        Masuk Ke Dashboard
    </a>
</div>
@endsection
