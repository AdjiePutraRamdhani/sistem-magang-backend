@extends('emails.layout')

@section('title', 'Status Pendaftaran Magang Diperbarui')

@section('content')
<h2 style="margin-top: 0; color: #1e3a8a; font-size: 20px; font-weight: 600;">Halo, {{ $pendaftaran->mahasiswa->user->nama_lengkap }}!</h2>
<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Pemberitahuan mengenai berkas pendaftaran magang Anda di <strong>Dinas Perpustakaan dan Kearsipan Provinsi Riau</strong>.
</p>

@if($pendaftaran->status === 'disetujui')
    <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; border-radius: 4px; padding: 20px; margin: 24px 0;">
        <h3 style="margin-top: 0; color: #065f46; font-size: 18px; font-weight: 700;">Selamat, Pendaftaran Anda DISETUJUI!</h3>
        <p style="font-size: 14px; color: #047857; margin: 8px 0 0 0; line-height: 1.5;">
            Anda telah resmi diterima sebagai peserta magang. Silakan mempersiapkan diri sesuai dengan jadwal magang yang telah ditentukan.
        </p>
    </div>

    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin: 24px 0;">
        <h4 style="margin-top: 0; color: #1e3a8a; font-size: 15px; font-weight: 600; margin-bottom: 12px;">Detail Pelaksanaan Magang:</h4>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="padding: 4px 0; font-size: 14px; color: #6b7280; width: 140px;">Tanggal Mulai:</td>
                <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->tanggal_mulai->format('d M Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Tanggal Selesai:</td>
                <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->tanggal_selesai->format('d M Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Pembimbing Instansi:</td>
                <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->pembimbing->user->nama_lengkap }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Kontak Pembimbing:</td>
                <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->pembimbing->user->no_telepon }}</td>
            </tr>
        </table>
    </div>
@else
    <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 4px; padding: 20px; margin: 24px 0;">
        <h3 style="margin-top: 0; color: #991b1b; font-size: 18px; font-weight: 700;">Mohon Maaf, Pendaftaran Anda DITOLAK.</h3>
        <p style="font-size: 14px; color: #b91c1c; margin: 8px 0 0 0; line-height: 1.5;">
            Berkas pendaftaran magang Anda belum dapat kami setujui saat ini.
        </p>
    </div>

    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 24px 0;">
        <h4 style="margin-top: 0; color: #991b1b; font-size: 15px; font-weight: 600; margin-bottom: 8px;">Alasan Penolakan:</h4>
        <p style="margin: 0; font-size: 14px; color: #4b5563; line-height: 1.6; font-style: italic;">
            "{{ $pendaftaran->alasan_tolak }}"
        </p>
    </div>
    
    <p style="font-size: 15px; line-height: 1.6; color: #4b5563;">
        Anda dapat mengajukan pendaftaran baru kembali dengan berkas surat pengantar yang telah diperbaiki.
    </p>
@endif

<div style="text-align: center; margin: 32px 0 16px 0;">
    <a href="{{ config('app.url') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px 0 rgba(37, 99, 235, 0.2);">
        Lihat Detail di Dashboard
    </a>
</div>
@endsection
