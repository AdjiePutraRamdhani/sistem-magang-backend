@extends('emails.layout')

@section('title', 'Penilaian Magang Selesai di-Input')

@section('content')
<h2 style="margin-top: 0; color: #1e3a8a; font-size: 20px; font-weight: 600;">Halo, {{ $pendaftaran->mahasiswa->user->nama_lengkap }}!</h2>
<p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
    Kami ingin menginformasikan bahwa pembimbing instansi Anda, <strong>{{ $pendaftaran->pembimbing->user->nama_lengkap }}</strong>, telah menyelesaikan penginputan penilaian akhir magang Anda.
</p>

<div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 24px; margin: 24px 0; text-align: center;">
    <h3 style="margin: 0; color: #166534; font-size: 16px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Nilai Rata-rata Akhir</h3>
    <div style="font-size: 48px; font-weight: 800; color: #15803d; margin: 12px 0;">
        {{ $pendaftaran->penilaian->nilai_total }}
    </div>
    <div style="display: inline-block; background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 9999px; font-size: 14px; font-weight: 600;">
        Status: Selesai Dinilai
    </div>
</div>

<div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 24px 0;">
    <h4 style="margin-top: 0; color: #1e3a8a; font-size: 15px; font-weight: 600; margin-bottom: 12px;">Rincian Penilaian:</h4>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #4b5563; border-bottom: 1px solid #f3f4f6;">Kedisiplinan:</td>
            <td style="padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $pendaftaran->penilaian->kedisiplinan }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #4b5563; border-bottom: 1px solid #f3f4f6;">Kemampuan Teknis:</td>
            <td style="padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $pendaftaran->penilaian->kemampuan_teknis }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #4b5563; border-bottom: 1px solid #f3f4f6;">Sikap & Etika:</td>
            <td style="padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $pendaftaran->penilaian->sikap }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #4b5563;">Kehadiran:</td>
            <td style="padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">{{ $pendaftaran->penilaian->kehadiran }}</td>
        </tr>
    </table>
    
    @if($pendaftaran->penilaian->catatan)
        <div style="margin-top: 16px; border-top: 1px dashed #e5e7eb; padding-top: 16px;">
            <h5 style="margin: 0 0 6px 0; color: #4b5563; font-size: 13px; font-weight: 600;">Catatan Pembimbing:</h5>
            <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5; font-style: italic;">
                "{{ $pendaftaran->penilaian->catatan }}"
            </p>
        </div>
    @endif
</div>

<p style="font-size: 15px; line-height: 1.6; color: #4b5563;">
    Sertifikat magang resmi Anda sedang disiapkan dan akan segera diunggah oleh pihak instansi. Silakan memantau dashboard Anda secara berkala.
</p>

<div style="text-align: center; margin: 32px 0 16px 0;">
    <a href="{{ config('app.url') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px 0 rgba(37, 99, 235, 0.2);">
        Buka Dashboard
    </a>
</div>
@endsection
