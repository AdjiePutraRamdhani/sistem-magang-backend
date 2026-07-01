<?php

namespace App\Mail;

use App\Models\PendaftaranMagang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PenilaianUploadedMail extends Mailable
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
        return new Envelope(
            subject: 'Penilaian Magang Anda Telah Selesai di-Input',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.mahasiswa.nilai-uploaded',
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
