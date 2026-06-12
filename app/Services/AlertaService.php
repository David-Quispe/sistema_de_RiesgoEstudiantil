<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\Usuario;

/**
 * AlertaService
 *
 * Centraliza la lógica de detección y creación de alertas.
 * Se invoca desde:
 *   - CreateEntrevista / EditEntrevista (inmediato, tras calcularRiesgo)
 *   - DetectarRiesgoJob (programado, para deterioro progresivo)
 */
class AlertaService
{
    /**
     * Evalúa una entrevista recién calculada y genera alertas si corresponde.
     * Llamar DESPUÉS de $entrevista->calcularRiesgo().
     */
    public function evaluarEntrevista(Entrevista $entrevista): void
    {
        $entrevista->loadMissing(['estudiante.institucion', 'consejero', 'periodo']);

        if ($entrevista->nivel_riesgo === Entrevista::RIESGO_ALTO) {
            $this->crearAlertaRiesgoAlto($entrevista);
        }

        $this->evaluarDeterioroProgresivo($entrevista->estudiante);
    }

    /**
     * Crea una alerta de RIESGO_ALTO para el consejero y coordinadores
     * de la misma institución. Evita duplicados en el mismo periodo.
     */
    public function crearAlertaRiesgoAlto(Entrevista $entrevista): void
    {
        $estudiante = $entrevista->estudiante;
        $mensaje    = "⚠️ El estudiante {$estudiante->nombre_completo} "
                    . "({$estudiante->codigo}) ha sido clasificado con RIESGO ALTO "
                    . "en la entrevista del {$entrevista->fecha_entrevista->format('d/m/Y')}. "
                    . "Puntaje: {$entrevista->puntaje_total}.";

        // Destinatarios: el consejero que registró + coordinadores de la institución
        $destinatarios = Usuario::where('institucion_id', $estudiante->institucion_id)
            ->where('activo', 1)
            ->whereIn('rol', [
                Usuario::ROL_CONSEJERO,
                Usuario::ROL_COORDINADOR,
                Usuario::ROL_BIENESTAR,
            ])
            ->get();

        foreach ($destinatarios as $usuario) {
            // Evitar duplicado: misma entrevista + mismo usuario + mismo tipo
            $yaExiste = Alerta::where('estudiante_id', $estudiante->id)
                ->where('usuario_id', $usuario->id)
                ->where('tipo', Alerta::TIPO_RIESGO_ALTO)
                ->where('leida', 0)
                ->whereDate('created_at', '>=', now()->subDays(1))
                ->exists();

            if (! $yaExiste) {
                Alerta::create([
                    'estudiante_id' => $estudiante->id,
                    'usuario_id'    => $usuario->id,
                    'tipo'          => Alerta::TIPO_RIESGO_ALTO,
                    'canal'         => 'sistema',
                    'mensaje'       => $mensaje,
                    'leida'         => false,
                ]);
            }
        }
    }

    /**
     * Detecta deterioro progresivo: si las últimas 2 entrevistas del estudiante
     * fueron MEDIO y ahora es ALTO, o si subió de BAJO a ALTO en 2 entrevistas.
     * Genera alerta de tipo DETERIORO_PROGRESIVO.
     */
    public function evaluarDeterioroProgresivo(Estudiante $estudiante): void
    {
        // Tomar las últimas 3 entrevistas ordenadas de más reciente a más antigua
        $ultimas = Entrevista::where('estudiante_id', $estudiante->id)
            ->whereNotNull('nivel_riesgo')
            ->orderByDesc('fecha_entrevista')
            ->limit(3)
            ->pluck('nivel_riesgo')
            ->toArray();

        // Necesitamos al menos 2 para comparar
        if (count($ultimas) < 2) {
            return;
        }

        $actual   = $ultimas[0]; // más reciente
        $anterior = $ultimas[1];

        // Mapa numérico: mayor número = mayor riesgo
        $escala = ['BAJO' => 1, 'MEDIO' => 2, 'ALTO' => 3];

        $nivelActual   = $escala[$actual]   ?? 0;
        $nivelAnterior = $escala[$anterior] ?? 0;

        // Solo alertar si empeoró (subió de nivel)
        if ($nivelActual <= $nivelAnterior) {
            return;
        }

        $mensaje = "📈 Deterioro progresivo detectado en {$estudiante->nombre_completo} "
                 . "({$estudiante->codigo}): pasó de {$anterior} a {$actual}. "
                 . "Se recomienda seguimiento inmediato.";

        $destinatarios = Usuario::where('institucion_id', $estudiante->institucion_id)
            ->where('activo', 1)
            ->whereIn('rol', [
                Usuario::ROL_COORDINADOR,
                Usuario::ROL_BIENESTAR,
            ])
            ->get();

        foreach ($destinatarios as $usuario) {
            $yaExiste = Alerta::where('estudiante_id', $estudiante->id)
                ->where('usuario_id', $usuario->id)
                ->where('tipo', Alerta::TIPO_DETERIORO_PROGRESIVO)
                ->where('leida', 0)
                ->whereDate('created_at', '>=', now()->subDays(3))
                ->exists();

            if (! $yaExiste) {
                Alerta::create([
                    'estudiante_id' => $estudiante->id,
                    'usuario_id'    => $usuario->id,
                    'tipo'          => Alerta::TIPO_DETERIORO_PROGRESIVO,
                    'canal'         => 'sistema',
                    'mensaje'       => $mensaje,
                    'leida'         => false,
                ]);
            }
        }
    }

    /**
     * Recorre todos los estudiantes activos y evalúa su deterioro progresivo.
     * Se usa desde el Job programado (scheduler diario).
     */
    public function escanearTodosLosEstudiantes(): int
    {
        $estudiantes = Estudiante::where('activo', 1)
            ->whereHas('entrevistas') // solo los que tienen al menos 1 entrevista
            ->get();

        $alertasGeneradas = 0;

        foreach ($estudiantes as $estudiante) {
            $antes = Alerta::where('estudiante_id', $estudiante->id)->count();
            $this->evaluarDeterioroProgresivo($estudiante);
            $despues = Alerta::where('estudiante_id', $estudiante->id)->count();
            $alertasGeneradas += ($despues - $antes);
        }

        return $alertasGeneradas;
    }
}
