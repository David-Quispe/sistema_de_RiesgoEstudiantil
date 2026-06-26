<?php

namespace App\Mail;

use App\Models\Entrevista;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * RiesgoAltoMail
 *
 * Alerta por correo cuando una entrevista clasifica a un estudiante
 * en RIESGO ALTO (RF13 / CUS07).
 */
class RiesgoAltoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Entrevista $entrevista)
    {
        $this->entrevista->loadMissing('estudiante', 'consejero');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔴 Alerta de Riesgo Alto — {$this->entrevista->estudiante->nombre_completo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.riesgo-alto',
            with: [
                'entrevista' => $this->entrevista,
                'estudiante' => $this->entrevista->estudiante,
            ],
        );
    }
}
