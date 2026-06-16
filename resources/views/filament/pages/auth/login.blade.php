<x-filament-panels::page.simple>
    {{-- Logo y encabezado institucional --}}
    <div class="flex flex-col items-center gap-3 mb-6">
        <img
            src="https://www.tecsup.edu.pe/wp-content/uploads/2024/07/Group-680.png"
            alt="TECSUP"
            class="h-14 w-auto object-contain"
        />
        <div class="text-center">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">SMER</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Sistema de Monitoreo y Detección Temprana de Riesgo Estudiantil
            </p>
        </div>
    </div>

    {{-- Formulario de login de Filament --}}
    @livewire('filament-panels::auth.login')

    {{-- Pie de página --}}
    <p class="text-center text-xs text-gray-400 mt-6">
        © {{ date('Y') }} TECSUP · Todos los derechos reservados
    </p>
</x-filament-panels::page.simple>
