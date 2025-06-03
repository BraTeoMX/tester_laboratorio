<?php

use Livewire\Volt\Component;
use Livewire\WithPagination; // Necesario para la paginación
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    // Usamos el Trait de Paginación para manejar grandes listas de datos
    use WithPagination;

    // Con el método `with()` los datos estarán disponibles en la vista automáticamente.
    // Livewire es lo suficientemente inteligente como para no volver a ejecutar esto en cada render
    // a menos que sea necesario.
    public function with(): array
    {
        return [
            // 1. Obtenemos los usuarios
            // 2. Filtramos solo los que tienen `is_active = true`
            // 3. Ordenamos por nombre
            // 4. Paginamos los resultados, mostrando 10 por página
            'users' => User::orderBy('name')->paginate(10),
        ];
    }

}; ?>

<div>
    {{-- Encabezado de la página (usa el layout de Laravel Breeze/Jetstream) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Usuarios Activos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Contenedor para hacer la tabla responsiva en móviles --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha de Registro
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">
                                            No se encontraron usuarios activos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Enlaces de paginación. Livewire los hace funcionar con AJAX automáticamente --}}
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>