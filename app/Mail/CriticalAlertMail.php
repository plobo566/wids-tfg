<?php

namespace App\Mail;

use App\Models\Detection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

//Añadimos "implements ShouldQueue" para Horizon 
class CriticalAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Detection $detection;

    public function __construct(Detection $detection)
    {
        $this->detection = $detection;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ALERTA CRÍTICA WIDS: ' . $this->detection->rule_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.alerts.critical',
        );
    }
}