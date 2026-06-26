<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Ejecutivo SMER</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                {{-- Encabezado --}}
                <tr>
                    <td style="background:#1e3a5f; padding:20px 28px;">
                        <div style="color:#ffffff; font-size:18px; font-weight:bold; letter-spacing:0.5px;">SMER — TECSUP</div>
                        <div style="color:#cbd5e1; font-size:12px; padding-top:2px;">Sistema de Monitoreo y Detección Temprana de Riesgo Estudiantil</div>
                    </td>
                </tr>

                {{-- Contenido --}}
                <tr>
                    <td style="padding:28px;">
                        <h2 style="margin:0 0 4px; font-size:17px; color:#0f172a;">📊 Reporte Ejecutivo de Riesgo Estudiantil</h2>
                        <p style="margin:0 0 20px; font-size:13px; color:#64748b;">
                            {{ $institucionNombre }} — Frecuencia: {{ $frecuenciaLabel }} — Periodo: {{ $periodoLabel }}
                        </p>

                        <p style="font-size:13px; color:#334155; line-height:1.6;">
                            Estimado(a) directivo(a),<br><br>
                            Se adjunta el reporte ejecutivo de riesgo estudiantil correspondiente a este periodo.
                            A continuación un resumen general:
                        </p>

                        {{-- Resumen --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0 20px;">
                            <tr>
                                <td width="25%" align="center" style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 4px;">
                                    <div style="font-size:20px; font-weight:bold; color:#b91c1c;">{{ $resumen['alto'] ?? 0 }}</div>
                                    <div style="font-size:9px; color:#b91c1c; text-transform:uppercase;">Riesgo Alto</div>
                                </td>
                                <td width="4"></td>
                                <td width="25%" align="center" style="background:#fef9c3; border:1px solid #fde68a; border-radius:8px; padding:12px 4px;">
                                    <div style="font-size:20px; font-weight:bold; color:#92400e;">{{ $resumen['medio'] ?? 0 }}</div>
                                    <div style="font-size:9px; color:#92400e; text-transform:uppercase;">Riesgo Medio</div>
                                </td>
                                <td width="4"></td>
                                <td width="25%" align="center" style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 4px;">
                                    <div style="font-size:20px; font-weight:bold; color:#166534;">{{ $resumen['bajo'] ?? 0 }}</div>
                                    <div style="font-size:9px; color:#166534; text-transform:uppercase;">Riesgo Bajo</div>
                                </td>
                                <td width="4"></td>
                                <td width="25%" align="center" style="background:#e0e7ff; border:1px solid #a5b4fc; border-radius:8px; padding:12px 4px;">
                                    <div style="font-size:20px; font-weight:bold; color:#3730a3;">{{ $resumen['total'] ?? 0 }}</div>
                                    <div style="font-size:9px; color:#3730a3; text-transform:uppercase;">Total</div>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:13px; color:#334155; line-height:1.6;">
                            El detalle completo, incluyendo estudiantes por carrera y ciclo, se encuentra en el PDF adjunto a este correo.
                        </p>
                    </td>
                </tr>

                {{-- Pie --}}
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
