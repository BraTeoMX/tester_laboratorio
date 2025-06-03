<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\CatalogoRol;
// 1. 👇 Importamos la FACHADA correcta, como indica la documentación.
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new class extends Component {
    use WithPagination;

    // 2. ✅ Los listeners siguen siendo la forma correcta de recibir eventos.
    protected $listeners = [
        'toggleStatusConfirmed'
    ];

    public function with(): array
    {
        return [
            'users' => User::with('role')->orderBy('name')->paginate(10),
        ];
    }

    /**
     * 3. ✅ MÉTODO CORREGIDO: Usando la sintaxis fluida de la Fachada.
     */
    public function confirmToggleStatus(User $user)
    {
        LivewireAlert::title('¿Estás seguro?')
            ->text('Se cambiará el estado del usuario.')
            ->question() // Usa el ícono de pregunta
            ->withConfirmButton('Sí, cambiar') // Botón de confirmación
            ->withCancelButton('No, cancelar') // Botón de cancelación
            ->onConfirm('toggleStatusConfirmed', ['userId' => $user->id]) // Evento y datos al confirmar
            ->show();
    }

    /**
     * 4. ✅ Este método es correcto, solo ajustamos la alerta de éxito.
     */
    public function toggleStatusConfirmed($event)
    {
        $user = User::find($event['userId']);
        if ($user) {
            $user->status = !$user->status;
            $user->save();
            
            // 5. ✅ Usamos la sintaxis de la Fachada también para la alerta de éxito.
            LivewireAlert::title('¡Hecho!')
                ->text('El estado del usuario ha sido actualizado.')
                ->success()
                ->toast() // Mostramos como una notificación "toast"
                ->position('top-end') // En la esquina superior derecha
                ->timer(3000) // Se cierra a los 3 segundos
                ->show();
        }
    }
};
?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Correo Electrónico
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Numero Empleado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Rol
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acción
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
                                            {{ $user->employee_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $user->role?->nombre ?? 'Sin rol' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            @if ($user->status == 1)
                                                <button wire:click="confirmToggleStatus({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                    Baja
                                                </button>
                                            @else
                                                <button wire:click="confirmToggleStatus({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                    Alta
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Ajustamos el colspan para que ocupe todas las columnas --}}
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">
                                            No se encontraron usuarios.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>