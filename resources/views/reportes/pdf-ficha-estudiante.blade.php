<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Individual del Estudiante — SMER</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
        }

        .header {
            background: #1e3a5f;
            color: white;
            padding: 18px 24px;
            margin-bottom: 18px;
        }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .header h1 { font-size: 19px; font-weight: bold; letter-spacing: 1px; }
        .header h2 { font-size: 12px; font-weight: normal; margin-top: 4px; opacity: 0.85; }
        .header-date { font-size: 10px; opacity: 0.75; text-align: right; }

        .contenido { padding: 0 24px; }

        /* Tarjeta perfil */
        .perfil {
            display: flex;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 18px;
            justify-content: space-between;
        }
        .perfil .campo { margin-bottom: 6px; }
        .perfil .campo .lbl { font-size: 9px; text-transform: uppercase; color: #64748b; letter-spacing: 0.4px; }
        .perfil .campo .val { font-size: 13px; font-weight: bold; color: #0f172a; }
        .perfil .col { width: 32%; }

        .badge-riesgo {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 14px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-alto  { background: #fee2e2; color: #b91c1c; }
        .badge-medio { background: #fef9c3; color: #92400e; }
        .badge-bajo  { background: #dcfce7; color: #166534; }
        .badge-gray  { background: #e2e8f0; color: #475569; }

        /* Secciones */
        .seccion-titulo {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a5f;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 4px;
            margin: 18px 0 10px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 14px; }
        thead tr { background: #1e3a5f; color: white; }
        thead th { padding: 7px 9px; text-align: left; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 9px; border-bottom: 1px solid #e5e7eb; }

        .obs-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 10px;
            color: #78350f;
            margin-bottom: 4px;
        }

        .sin-datos { text-align: center; padding: 16px; color: #9ca3af; font-style: italic; }

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

    <div class="header">
        <div class="header-top">
            <div>
                <h1>FICHA INDIVIDUAL DEL ESTUDIANTE</h1>
                <h2>SMER — Sistema de Monitoreo y Detección Temprana de Riesgo Estudiantil — TECSUP</h2>
            </div>
            <div class="header-date">
                Generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <div class="contenido">

        {{-- Perfil --}}
        <div class="perfil">
            <div class="col">
                <div class="campo"><div class="lbl">Código</div><div class="val">{{ $estudiante->codigo }}</div></div>
                <div class="campo"><div class="lbl">Nombre completo</div><div class="val">{{ $estudiante->nombre_completo }}</div></div>
                <div class="campo"><div class="lbl">Correo</div><div class="val">{{ $estudiante->email ?? '—' }}</div></div>
            </div>
            <div class="col">
                <div class="campo"><div class="lbl">Carrera</div><div class="val">{{ $estudiante->carrera }}</div></div>
                <div class="campo"><div class="lbl">Ciclo</div><div class="val">Ciclo {{ $estudiante->ciclo }}</div></div>
                <div class="campo"><div class="lbl">Institución</div><div class="val">{{ $estudiante->institucion->nombre ?? 'TECSUP' }}</div></div>
            </div>
            <div class="col">
                <div class="campo"><div class="lbl">Nivel de riesgo actual</div>
                    <div class="val">
                        @php $nivel = $estudiante->nivel_riesgo_actual; @endphp
                        <span class="badge-riesgo
                            @if($nivel === 'ALTO') badge-alto
                            @elseif($nivel === 'MEDIO') badge-medio
                            @elseif($nivel === 'BAJO') badge-bajo
                            @else badge-gray @endif">
                            {{ $nivel }}
                        </span>
                    </div>
                </div>
                <div class="campo"><div class="lbl">Total de entrevistas</div><div class="val">{{ $entrevistas->count() }}</div></div>
                <div class="campo"><div class="lbl">Última entrevista</div><div class="val">{{ $entrevistas->first()?->fecha_entrevista?->format('d/m/Y') ?? '—' }}</div></div>
            </div>
        </div>

        {{-- Historial de entrevistas --}}
        <div class="seccion-titulo">Historial de Entrevistas</div>
        @if($entrevistas->isEmpty())
            <div class="sin-datos">Este estudiante no tiene entrevistas registradas.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Periodo</th>
                        <th>Consejero</th>
                        <th>Puntaje</th>
                        <th>Riesgo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entrevistas as $e)
                        <tr>
                            <td>{{ $e->fecha_entrevista?->format('d/m/Y') }}</td>
                            <td>{{ $e->periodo->nombre ?? '—' }}</td>
                            <td>{{ $e->consejero->nombre_completo ?? '—' }}</td>
                            <td><strong>{{ number_format($e->puntaje_total, 2) }}</strong></td>
                            <td>
                                <span class="badge-riesgo
                                    @if($e->nivel_riesgo === 'ALTO') badge-alto
                                    @elseif($e->nivel_riesgo === 'MEDIO') badge-medio
                                    @else badge-bajo @endif">
                                    {{ $e->nivel_riesgo }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Indicadores de la última entrevista --}}
        @if($ultimaEntrevista && $ultimaEntrevista->indicadores->isNotEmpty())
        <div class="seccion-titulo">Indicadores — Última Entrevista ({{ $ultimaEntrevista->fecha_entrevista?->format('d/m/Y') }})</div>
        <table>
            <thead>
                <tr>
                    <th>Indicador</th>
                    <th>Puntaje (0–10)</th>
                    <th>Peso</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimaEntrevista->indicadores as $ind)
                    <tr>
                        <td>{{ $ind->nombre }}</td>
                        <td>{{ number_format($ind->puntaje, 2) }}</td>
                        <td>{{ number_format($ind->peso, 2) }}</td>
                        <td>{{ $ind->observacion ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Observaciones de entrevistas --}}
        @if($entrevistas->where('observaciones', '!=', null)->isNotEmpty())
        <div class="seccion-titulo">Observaciones Registradas</div>
        @foreach($entrevistas->where('observaciones', '!=', null) as $e)
            <div class="obs-box">
                <strong>{{ $e->fecha_entrevista?->format('d/m/Y') }}:</strong> {{ $e->observaciones }}
            </div>
        @endforeach
        @endif

        {{-- Derivaciones --}}
        <div class="seccion-titulo">Derivaciones a Bienestar Estudiantil</div>
        @if($derivaciones->isEmpty())
            <div class="sin-datos">No se registran derivaciones para este estudiante.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Motivo</th>
                        <th>Resolución</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($derivaciones as $d)
                        <tr>
                            <td>{{ $d->created_at?->format('d/m/Y') }}</td>
                            <td>{{ $d->prioridad }}</td>
                            <td>{{ $d->estado }}</td>
                            <td>{{ $d->motivo }}</td>
                            <td>{{ $d->resolucion ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

    <div class="footer">
        <span>SMER v1.0 — TECSUP Arequipa 2025</span>
        <span>Ficha individual generada automáticamente — Confidencial</span>
    </div>

</body>
</html>
