@extends('emails.layout')

@section('title', 'Sertifikat Magang Tersedia')

@section('content')
<h2 style="margin-top: 0; color: #1e3a8a; font-size: 20px; font-weight: 600;">Halo, {{ $pendaftaran->mahasiswa->user->nama_lengkap }}!</h2>
<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Selamat! Pihak instansi telah mengunggah sertifikat magang resmi Anda. Dengan demikian, seluruh rangkaian program magang Anda telah selesai dilaksanakan.
</p>

<div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <h3 style="margin-top: 0; color: #1e3a8a; font-size: 16px; font-weight: 600; margin-bottom: 12px;">Detail Sertifikat:</h3>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280; width: 140px;">No. Sertifikat:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->sertifikat->no_sertifikat }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Diterbitkan Pada:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $pendaftaran->sertifikat->diterbitkan_at->format('d M Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #6b7280;">Instansi Penerbit:</td>
            <td style="padding: 4px 0; font-size: 14px; color: #1f2937; font-weight: 600;">Dinas Perpustakaan dan Kearsipan Provinsi Riau</td>
        </tr>
    </table>
</div>

<p style="font-size: 15px; line-height: 1.6; color: #4b5563;">
    Terima kasih atas kontribusi dan dedikasi Anda selama masa magang. Semoga pengalaman dan ilmu yang diperoleh dapat bermanfaat untuk masa depan Anda.
</p>

<div style="text-align: center; margin: 32px 0 16px 0;">
    <a href="{{ config('app.url') }}" style="background-color: #10b981; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px 0 rgba(16, 185, 129, 0.2);">
        Unduh Sertifikat Magang
    </a>
</div>
@endsection
