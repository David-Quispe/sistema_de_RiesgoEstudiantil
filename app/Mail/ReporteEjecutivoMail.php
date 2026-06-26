<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ReporteEjecutivoMail
 *
 * Correo con el reporte ejecutivo de riesgo estudiantil (PDF adjunto)
 * enviado periódicamente a los directivos de la institución.
 *
 * Se dispara desde:
 *   - EnviarReporteEjecutivoCommand (manual: php artisan app:enviar-reporte-ejecutivo)
 *   - El scheduler en routes/console.php (automático: semanal/mensual)
 */
class ReporteEjecutivoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $institucionNombre;
    public string $periodoLabel;
    public string $frecuenciaLabel;
    public array  $resumen;

    /**
     * @param  string  $pdfContent  Contenido binario del PDF ya generado
     * @param  string  $pdfFilename Nombre de archivo del PDF adjunto
     * @param  array   $resumen     ['total' => int, 'alto' => int, 'medio' => int, 'bajo' => int]
     */
    public function __construct(
        string $institucionNombre,
        string $periodoLabel,
        string $frecuenciaLabel,
        array $resumen,
        public string $pdfContent,
        public string $pdfFilename,
    ) {
        $this->institucionNombre = $institucionNombre;
        $this->periodoLabel      = $periodoLabel;
        $this->frecuenciaLabel   = $frecuenciaLabel;
        $this->resumen           = $resumen;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📊 Reporte Ejecutivo SMER — {$this->institucionNombre} ({$this->frecuenciaLabel})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reporte-ejecutivo',
            with: [
                'institucionNombre' => $this->institucionNombre,
                'periodoLabel'      => $this->periodoLabel,
                'frecuenciaLabel'   => $this->frecuenciaLabel,
                'resumen'           => $this->resumen,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }
}
