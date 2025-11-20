<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperarSenhaMail extends Mailable {
    use Queueable, SerializesModels;

    public $usuario;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $url) {
        $this->usuario = $usuario;
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperar Senha',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(markdown: 'emails.recuperar_senha');
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
