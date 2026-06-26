<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alerta de Riesgo Alto SMER</title>
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
                        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:8px 14px; border-radius:6px; font-size:12px; font-weight:bold; margin-bottom:16px; display:inline-block;">
                            🔴 RIESGO ALTO DETECTADO
                        </div>

                        <h2 style="margin:0 0 4px; font-size:17px; color:#0f172a;">{{ $estudiante->nombre_completo }}</h2>
                        <p style="margin:0 0 20px; font-size:13px; color:#64748b;">Código: {{ $estudiante->codigo }} — {{ $estudiante->carrera }}, Ciclo {{ $estudiante->ciclo }}</p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; color:#334155; border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px 0; width:160px; color:#64748b;">Fecha de entrevista:</td>
                                <td style="padding:6px 0; font-weight:bold;">{{ $entrevista->fecha_entrevista?->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0; color:#64748b;">Puntaje total:</td>
                                <td style="padding:6px 0; font-weight:bold; color:#b91c1c;">{{ number_format($entrevista->puntaje_total, 2) }}</td>
                            </tr>
                            @if($entrevista->observaciones)
                            <tr>
                                <td style="padding:6px 0; color:#64748b; vertical-align:top;">Observaciones:</td>
                                <td style="padding:6px 0;">{{ $entrevista->observaciones }}</td>
                            </tr>
                            @endif
                        </table>

                        <p style="margin-top:20px; font-size:13px; color:#334155; line-height:1.6;">
                            Se recomienda revisar el caso y, de ser necesario, generar una derivación a Bienestar Estudiantil
                            desde el panel SMER.
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
