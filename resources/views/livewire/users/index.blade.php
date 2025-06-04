<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\CatalogoRol;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Rule;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    use WithPagination;

    // 🆕 1. Propiedad para manejar la visibilidad del modal
    public bool $isModalOpen = false;

    protected $listeners = [
        'toggleStatusConfirmed'
    ];

    // 🆕 2. Métodos para controlar el modal
    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function with(): array
    {
        return [
            'users' => User::with('role')->orderBy('name')->paginate(10),
        ];
    }

    public function confirmToggleStatus(User $user)
    {
        LivewireAlert::title('¿Estás seguro?')
            ->text('Se cambiará el estado del usuario.')
            ->question()
            ->withConfirmButton('Sí, cambiar')
            ->withCancelButton('No, cancelar')
            ->onConfirm('toggleStatusConfirmed', ['userId' => $user->id])
            ->show();
    }

    public function toggleStatusConfirmed($event)
    {
        $user = User::find($event['userId']);
        if ($user) {
            $user->status = !$user->status;
            $user->save();

            LivewireAlert::title('¡Hecho!')
                ->text('El estado del usuario ha sido actualizado.')
                ->success()
                ->toast()
                ->position('top-end')
                ->timer(3000)
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
                        <button
                            type="button"
                            wire:click="openModal"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        >
                            Crear Usuario
                        </button>
                    </div>
                    <br>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- ... Tu tabla de usuarios se mantiene igual ... --}}
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Correo Electrónico</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Numero Empleado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->employee_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->role?->nombre ?? 'Sin rol' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            @if ($user->status == 1)
                                                <button wire:click="confirmToggleStatus({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Baja</button>
                                            @else
                                                <button wire:click="confirmToggleStatus({{ $user->id }})" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Alta</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">No se encontraron usuarios.</td>
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
    
    @if ($isModalOpen)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50" wire:click="closeModal"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 m-4 max-w-lg w-full z-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Crear Nuevo Usuario
                </h3>
                <div class="text-gray-700 dark:text-gray-300 mb-6">
                    <p>Aquí se agregará el formulario para crear el usuario.</p>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>