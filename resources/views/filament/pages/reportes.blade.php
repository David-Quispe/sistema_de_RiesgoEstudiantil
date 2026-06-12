<x-filament-panels::page>

    {{-- Filtros --}}
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    {{-- Botones de exportación --}}
    <div class="flex gap-4 mt-4">
        <x-filament::button
            wire:click="exportarPDF"
            color="danger"
            icon="heroicon-o-document-arrow-down"
        >
            Exportar PDF
        </x-filament::button>

        <x-filament::button
            wire:click="exportarExcel"
            color="success"
            icon="heroicon-o-table-cells"
        >
            Exportar Excel (CSV)
        </x-filament::button>
    </div>

    {{-- Tabla de previsualización --}}
    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Vista previa del reporte
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $this->getDatosReporte()->count() }} registros encontrados
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Estudiante</th>
                        <th class="px-4 py-3">Carrera</th>
                        <th class="px-4 py-3">Ciclo</th>
                        <th class="px-4 py-3">Periodo</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Puntaje</th>
                        <th class="px-4 py-3">Riesgo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->getDatosReporte() as $e)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-mono">{{ $e->estudiante->codigo }}</td>
                            <td class="px-4 py-3 font-medium">{{ $e->estudiante->nombre_completo }}</td>
                            <td class="px-4 py-3">{{ $e->estudiante->carrera }}</td>
                            <td class="px-4 py-3 text-center">{{ $e->estudiante->ciclo }}</td>
                            <td class="px-4 py-3">{{ $e->periodo->nombre }}</td>
                            <td class="px-4 py-3">{{ $e->fecha_entrevista?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ number_format($e->puntaje_total, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($e->nivel_riesgo === 'ALTO')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                        🔴 ALTO
                                    </span>
                                @elseif($e->nivel_riesgo === 'MEDIO')
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                        🟡 MEDIO
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                        🟢 BAJO
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                No hay registros con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-filament-panels::page>
