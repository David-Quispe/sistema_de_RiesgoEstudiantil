<?php

namespace App\Filament\Resources\DerivacionResource\Pages;

use App\Filament\Resources\DerivacionResource;
use App\Mail\DerivacionNotificacionMail;
use App\Models\Usuario;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateDerivacion extends CreateRecord
{
    protected static string $resource = DerivacionResource::class;

    /**
     * Notifica por correo a Bienestar Estudiantil cuando se crea
     * una nueva derivación (RF12 / CUS06).
     */
    protected function afterCreate(): void
    {
        $derivacion = $this->record;
        $derivacion->loadMissing('entrevista.estudiante.institucion');

        $institucionId = $derivacion->entrevista->estudiante->institucion_id ?? null;

        $destinatarios = Usuario::where('rol', Usuario::ROL_BIENESTAR)
            ->where('activo', 1)
            ->when($institucionId, fn ($q) => $q->where('institucion_id', $institucionId))
            ->whereNotNull('email')
            ->pluck('email');

        if ($destinatarios->isEmpty()) {
            Log::warning('[SMER] Derivación creada sin destinatarios de Bienestar con correo configurado.', [
                'derivacion_id' => $derivacion->id,
            ]);
            return;
        }

        foreach ($destinatarios as $correo) {
            Mail::to($correo)->send(new DerivacionNotificacionMail($derivacion));
        }
    }
}
