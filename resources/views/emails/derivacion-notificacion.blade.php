<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva derivación SMER</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                <tr>
                    <td style="background:#1e3a5f; padding:20px 28px;">
                        <div style="color:#ffffff; font-size:18px; font-weight:bold; letter-spacing:0.5px;">SMER — TECSUP</div>
                        <div style="color:#cbd5e1; font-size:12px; padding-top:2px;">Sistema de Monitoreo y Detección Temprana de Riesgo Estudiantil</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px;">
                        @if($derivacion->prioridad === 'URGENTE')
                            <div style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:8px 14px; border-radius:6px; font-size:12px; font-weight:bold; margin-bottom:16px;">
                                🚨 PRIORIDAD URGENTE
                            </div>
                        @endif

                        <h2 style="margin:0 0 4px; font-size:17px; color:#0f172a;">Nueva derivación a Bienestar Estudiantil</h2>
                        <p style="margin:0 0 20px; font-size:13px; color:#64748b;">Se ha registrado un caso que requiere atención.</p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; color:#334155; border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px 0; width:140px; color:#64748b;">Estudiante:</td>
                                <td style="padding:6px 0; font-weight:bold;">{{ $estudiante->nombre_completo }} ({{ $estudiante->codigo }})</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0; color:#64748b;">Carrera / Ciclo:</td>
                                <td style="padding:6px 0;">{{ $estudiante->carrera }} — Ciclo {{ $estudiante->ciclo }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0; color:#64748b;">Derivado por:</td>
                                <td style="padding:6px 0;">{{ $consejero->nombre_completo }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0; color:#64748b;">Prioridad:</td>
                                <td style="padding:6px 0;">{{ $derivacion->prioridad }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0; color:#64748b; vertical-align:top;">Motivo:</td>
                                <td style="padding:6px 0;">{{ $derivacion->motivo }}</td>
                            </tr>
                        </table>

                        <p style="margin-top:20px; font-size:13px; color:#334155;">
                            Ingresa al sistema SMER para tomar este caso y registrar su atención.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="background:#f8fafc; padding:16px 28px; border-top:1px solid #e2e8f0;">
                        <p style="margin:0; font-size:11px; color:#94a3b8;">Este es un correo automático generado por SMER. Por favor no responder directamente a este mensaje.</p>
                        <p style="margin:4px 0 0; font-size:11px; color:#94a3b8;">TECSUP Arequipa — {{ now()->format('Y') }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
