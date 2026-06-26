<?php

namespace App\Mail;

use App\Models\Derivacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * DerivacionNotificacionMail
 *
 * Notifica por correo a Bienestar Estudiantil cuando se crea una nueva
 * derivación (RF12 / CUS06).
 */
class DerivacionNotificacionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Derivacion $derivacion)
    {
        $this->derivacion->loadMissing('entrevista.estudiante', 'consejero');
    }

    public function envelope(): Envelope
    {
        $prioridad = $this->derivacion->prioridad;
        $prefijo   = $prioridad === 'URGENTE' ? '🚨 URGENTE — ' : '';

        return new Envelope(
            subject: "{$prefijo}Nueva derivación SMER — {$this->derivacion->entrevista->estudiante->nombre_completo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.derivacion-notificacion',
            with: [
                'derivacion' => $this->derivacion,
                'estudiante' => $this->derivacion->entrevista->estudiante,
                'consejero'  => $this->derivacion->consejero,
            ],
        );
    }
}
