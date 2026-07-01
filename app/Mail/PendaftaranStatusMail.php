<?php

namespace App\Mail;

use App\Models\PendaftaranMagang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftaran;

    /**
     * Create a new message instance.
     */
    public function __construct(PendaftaranMagang $pendaftaran)
    {
        $this->pendaftaran = $pendaftaran;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusStr = $this->pendaftaran->status === 'disetujui' ? 'DISETUJUI' : 'DITOLAK';
        return new Envelope(
            subject: 'Status Pendaftaran Magang Anda: ' . $statusStr,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.mahasiswa.pendaftaran-status',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
