<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo ?? 'SMER — TECSUP' }}</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                    {{-- Encabezado --}}
                    <tr>
                        <td style="background:#1e3a5f; padding:20px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="color:#ffffff; font-size:18px; font-weight:bold; letter-spacing:0.5px;">
                                        SMER — TECSUP
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#cbd5e1; font-size:12px; padding-top:2px;">
                                        Sistema de Monitoreo y Detección Temprana de Riesgo Estudiantil
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Contenido --}}
                    <tr>
                        <td style="padding:28px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Pie --}}
                    <tr>
                        <td style="background:#f8fafc; padding:16px 28px; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; font-size:11px; color:#94a3b8;">
                                Este es un correo automático generado por SMER. Por favor no responder directamente a este mensaje.
                            </p>
                            <p style="margin:4px 0 0; font-size:11px; color:#94a3b8;">
                                TECSUP Arequipa — {{ now()->format('Y') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
