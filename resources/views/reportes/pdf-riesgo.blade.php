<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Riesgo Estudiantil — SMER</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
        }

        /* Encabezado */
        .header {
            background: #1e40af;
            color: white;
            padding: 18px 24px;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 13px;
            font-weight: normal;
            margin-top: 4px;
            opacity: 0.85;
        }
        .header-date {
            font-size: 10px;
            opacity: 0.75;
            text-align: right;
        }

        /* Filtros aplicados */
        .filtros {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 10px 16px;
            margin: 0 24px 16px 24px;
            display: flex;
            gap: 24px;
            font-size: 10px;
            color: #1e40af;
        }
        .filtros strong { font-weight: bold; }

        /* Resumen de contadores */
        .resumen {
            display: flex;
            gap: 12px;
            margin: 0 24px 16px 24px;
        }
        .stat-box {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
        }
        .stat-alto  { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .stat-medio { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }
        .stat-bajo  { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .stat-total { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc; }
        .stat-box .num  { font-size: 22px; display: block; }
        .stat-box .lbl  { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Tabla */
        .tabla-wrap { padding: 0 24px; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead tr {
            background: #1e40af;
            color: white;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Badges de riesgo */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-alto  { background: #fee2e2; color: #b91c1c; }
        .badge-medio { background: #fef9c3; color: #92400e; }
        .badge-bajo  { background: #dcfce7; color: #166534; }

        /* Pie de página */
        .footer {
            margin-top: 24px;
            padding: 10px 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="header">
        <div class="header-top">
            <div>
                <h1>SMER — TECSUP</h1>
                <h2>Sistema de Monitoreo y Detección Temprana de Riesgo Estudiantil</h2>
            </div>
            <div class="header-date">
                Generado el {{ now()->format('d/m/Y H:i') }}<br>
                Reporte de Riesgo Estudiantil
            </div>
        </div>
    </div>

    {{-- Filtros aplicados --}}
    <div class="filtros">
        <span><strong>Periodo:</strong> {{ $periodo }}</span>
        <span><strong>Carrera:</strong> {{ $carrera }}</span>
        <span><strong>Nivel de riesgo:</strong> {{ $nivel }}</span>
        <span><strong>Total registros:</strong> {{ $datos->count() }}</span>
    </div>

    {{-- Contadores --}}
    <div class="resumen">
        <div class="stat-box stat-alto">
            <span class="num">{{ $datos->where('nivel_riesgo','ALTO')->count() }}</span>
            <span class="lbl">🔴 Riesgo Alto</span>
        </div>
        <div class="stat-box stat-medio">
            <span class="num">{{ $datos->where('nivel_riesgo','MEDIO')->count() }}</span>
            <span class="lbl">🟡 Riesgo Medio</span>
        </div>
        <div class="stat-box stat-bajo">
            <span class="num">{{ $datos->where('nivel_riesgo','BAJO')->count() }}</span>
            <span class="lbl">🟢 Riesgo Bajo</span>
        </div>
        <div class="stat-box stat-total">
            <span class="num">{{ $datos->count() }}</span>
            <span class="lbl">Total Evaluados</span>
        </div>
    </div>

    {{-- Tabla de datos --}}
    <div class="tabla-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Ciclo</th>
                    <th>Periodo</th>
                    <th>Fecha</th>
                    <th>Consejero</th>
                    <th>Puntaje</th>
                    <th>Riesgo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $i => $e)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $e->estudiante->codigo }}</strong></td>
                        <td>{{ $e->estudiante->nombre_completo }}</td>
                        <td>{{ $e->estudiante->carrera }}</td>
                        <td style="text-align:center">{{ $e->estudiante->ciclo }}</td>
                        <td>{{ $e->periodo->nombre }}</td>
                        <td>{{ $e->fecha_entrevista?->format('d/m/Y') }}</td>
                        <td>{{ $e->consejero->nombre_completo }}</td>
                        <td style="text-align:center"><strong>{{ number_format($e->puntaje_total,2) }}</strong></td>
                        <td>
                            @if($e->nivel_riesgo === 'ALTO')
                                <span class="badge badge-alto">ALTO</span>
                            @elseif($e->nivel_riesgo === 'MEDIO')
                                <span class="badge badge-medio">MEDIO</span>
                            @else
                                <span class="badge badge-bajo">BAJO</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:20px; color:#9ca3af;">
                            No hay registros con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pie de página --}}
    <div class="footer">
        <span>SMER v1.0 — TECSUP Arequipa 2025</span>
        <span>Documento generado automáticamente — Confidencial</span>
    </div>

</body>
</html>
