<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Análisis de indicadores — Última entrevista
            @if($fechaEntrevista)
                <span class="text-sm font-normal text-gray-500 ml-2">({{ $fechaEntrevista }})</span>
            @endif
        </x-slot>

        @if(empty($etiquetas))
            <p class="text-sm text-gray-400 italic">Este estudiante aún no tiene entrevistas registradas.</p>
        @else

            {{-- ── Ficha compacta del estudiante ── --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                {{-- Iniciales --}}
                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-content-center items-center justify-center text-white font-bold text-lg"
                     style="background-color: {{ $nivelRiesgo === 'ALTO' ? '#DC2626' : ($nivelRiesgo === 'MEDIO' ? '#D97706' : '#16A34A') }}">
                    {{ strtoupper(substr($nombreEstudiante ?? 'E', 0, 1)) }}
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 dark:text-white text-base truncate">{{ $nombreEstudiante }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $codigoEstudiante }} · {{ $carreraEstudiante }} · Ciclo {{ $cicloEstudiante }}
                    </p>
                </div>
                {{-- Stats rápidos --}}
                <div class="flex gap-4 text-center flex-shrink-0">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Puntaje</p>
                        <p class="text-xl font-bold mt-0.5
                            {{ $nivelRiesgo === 'ALTO' ? 'text-red-600' : ($nivelRiesgo === 'MEDIO' ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ number_format($puntajeTotal, 2) }}
                        </p>
                    </div>
                    <div class="border-l border-gray-200 dark:border-gray-600 pl-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Entrevistas</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white mt-0.5">{{ $totalEntrevistas }}</p>
                    </div>
                    <div class="border-l border-gray-200 dark:border-gray-600 pl-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Última</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mt-0.5">{{ $fechaEntrevista }}</p>
                    </div>
                    <div class="border-l border-gray-200 dark:border-gray-600 pl-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nivel</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-bold
                            {{ $nivelRiesgo === 'ALTO' ? 'bg-red-100 text-red-700' : ($nivelRiesgo === 'MEDIO' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            {{ $nivelRiesgo }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Hexágono SVG dinámico --}}
            @php
                $cx = 340; $cy = 255;
                $posiciones = [
                    0 => [$cx,        $cy - 155],
                    1 => [$cx - 184,  $cy - 90],
                    2 => [$cx + 184,  $cy - 90],
                    3 => [$cx - 184,  $cy + 90],
                    4 => [$cx + 184,  $cy + 90],
                    5 => [$cx,        $cy + 155],
                ];
                // Colores por puntaje
                $colorPorPuntaje = function($p) {
                    if ($p >= 7) return ['fill'=>'#DCFCE7','stroke'=>'#16A34A','text'=>'#14532D','bar_bg'=>'#BBF7D0','bar_fg'=>'#16A34A','sub'=>'#15803D'];
                    if ($p >= 4) return ['fill'=>'#FEF3C7','stroke'=>'#D97706','text'=>'#92400E','bar_bg'=>'#FDE68A','bar_fg'=>'#D97706','sub'=>'#B45309'];
                    return ['fill'=>'#FEE2E2','stroke'=>'#DC2626','text'=>'#7F1D1D','bar_bg'=>'#FECACA','bar_fg'=>'#DC2626','sub'=>'#B91C1C'];
                };
                // Color centro por nivel
                $centroColor = $nivelRiesgo === 'ALTO' ? '#DC2626' : ($nivelRiesgo === 'MEDIO' ? '#7C3AED' : '#16A34A');
                $centroFill  = $nivelRiesgo === 'ALTO' ? '#FEE2E2' : ($nivelRiesgo === 'MEDIO' ? '#EDE9FE' : '#DCFCE7');
            @endphp

            <svg width="100%" viewBox="0 0 680 520" xmlns="http://www.w3.org/2000/svg" style="display:block;">

                {{-- Líneas desde centro a cada nodo --}}
                @foreach($posiciones as $i => $pos)
                    <line x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $pos[0] }}" y2="{{ $pos[1] }}"
                          stroke="{{ $centroColor }}" stroke-width="1" opacity="0.3"/>
                @endforeach

                {{-- Centro --}}
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="62" fill="{{ $centroColor }}" opacity="0.12"/>
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="50" fill="{{ $centroColor }}" opacity="0.20"/>
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="38" fill="{{ $centroColor }}" opacity="0.50"/>
                <text x="{{ $cx }}" y="{{ $cy - 14 }}" text-anchor="middle" font-size="10" font-weight="600"
                      fill="{{ $centroFill }}" font-family="sans-serif">RIESGO</text>
                <text x="{{ $cx }}" y="{{ $cy + 4 }}" text-anchor="middle" font-size="14" font-weight="700"
                      fill="white" font-family="sans-serif">{{ $nivelRiesgo }}</text>
                <text x="{{ $cx }}" y="{{ $cy + 20 }}" text-anchor="middle" font-size="10"
                      fill="{{ $centroFill }}" font-family="sans-serif">{{ number_format($puntajeTotal, 2) }} pts</text>

                {{-- Nodos --}}
                @foreach($etiquetas as $i => $nombre)
                    @php
                        $pos    = $posiciones[$i];
                        $px     = $pos[0];
                        $py     = $pos[1];
                        $p      = $puntajes[$i];
                        $peso   = $pesos[$i];
                        $c      = $colorPorPuntaje($p);
                        $bw     = 124;
                        $barPct = ($p / 10) * $bw;
                        // partir nombre en dos líneas si es largo
                        $partes = explode(' ', $nombre, 2);
                        $l1 = $partes[0] ?? '';
                        $l2 = $partes[1] ?? '';
                        // offset vertical del nodo según posición
                        $oy = ($py < $cy) ? -38 : -38; // siempre centrado en py
                    @endphp
                    <rect x="{{ $px - 82 }}" y="{{ $py - 38 }}" width="164" height="76" rx="12"
                          fill="{{ $c['fill'] }}" stroke="{{ $c['stroke'] }}" stroke-width="1.2"/>
                    @if($l2)
                        <text x="{{ $px }}" y="{{ $py - 16 }}" text-anchor="middle" font-size="11"
                              font-weight="600" fill="{{ $c['text'] }}" font-family="sans-serif">{{ $l1 }}</text>
                        <text x="{{ $px }}" y="{{ $py - 3 }}" text-anchor="middle" font-size="11"
                              font-weight="600" fill="{{ $c['text'] }}" font-family="sans-serif">{{ $l2 }}</text>
                    @else
                        <text x="{{ $px }}" y="{{ $py - 8 }}" text-anchor="middle" font-size="11"
                              font-weight="600" fill="{{ $c['text'] }}" font-family="sans-serif">{{ $l1 }}</text>
                    @endif
                    {{-- mini barra --}}
                    <rect x="{{ $px - 62 }}" y="{{ $py + 10 }}" width="{{ $bw }}" height="5" rx="2.5"
                          fill="{{ $c['bar_bg'] }}"/>
                    <rect x="{{ $px - 62 }}" y="{{ $py + 10 }}" width="{{ $barPct }}" height="5" rx="2.5"
                          fill="{{ $c['bar_fg'] }}"/>
                    <text x="{{ $px }}" y="{{ $py + 28 }}" text-anchor="middle" font-size="10"
                          fill="{{ $c['sub'] }}" font-family="sans-serif">{{ $p }} / 10 · peso {{ $peso }}</text>
                @endforeach

            </svg>


            {{-- Tabla detallada de indicadores --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Indicador</th>
                            <th class="px-4 py-3 text-center">Peso</th>
                            <th class="px-4 py-3 text-center">Puntaje</th>
                            <th class="px-4 py-3 text-center">Aporte</th>
                            <th class="px-4 py-3">Nivel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($etiquetas as $i => $nombre)
                            @php
                                $puntaje = $puntajes[$i];
                                $peso    = $pesos[$i];
                                $aporte  = round($puntaje * $peso, 2);
                                $pct     = ($puntaje / 10) * 100;
                                $color   = $puntaje >= 7 ? 'bg-green-400'
                                         : ($puntaje >= 4 ? 'bg-yellow-400' : 'bg-red-400');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $nombre }}</td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ $peso }}</td>
                                <td class="px-4 py-3 text-center font-bold
                                    {{ $puntaje >= 7 ? 'text-green-600' : ($puntaje >= 4 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $puntaje }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $aporte }}</td>
                                <td class="px-4 py-3 w-40">
                                    <div class="h-2 rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $color }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </x-filament::section>
</x-filament-widgets::widget>
