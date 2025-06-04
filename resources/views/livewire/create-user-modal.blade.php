<div>
    {{-- El panel del modal con estilos de Tailwind --}}
    <div class="p-6 bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:p-8">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Crear Usuario
            </h2>
            {{-- Botón de cierre (opcional pero recomendado) --}}
            <button x-on:click="close" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Cuerpo --}}
        <div class="mt-4 text-gray-600 dark:text-gray-400">
            <p>
                Aquí irá el formulario para crear un nuevo usuario.
            </p>
        </div>

    </div>
</div>