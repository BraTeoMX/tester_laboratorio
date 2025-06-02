<?php

use Illuminate\Support\Facades\Hash;
use function Livewire\Volt\state;
use function Livewire\Volt\mount; // 1. Importa la función 'mount'
use function Livewire\Volt\action;
use App\Models\User;
use App\Models\CatalogoRol;

state('customers', []);

// --- NUEVO: Estado para el modal y el formulario ---
state('showModal', false);
state('name', '');
state('email', '');
state('employee_number', '');
state('role_id', '0'); // Valor por defecto
state('password', '');
state('password_confirmation', '');
// --- FIN NUEVO ---

// 2. Usa la función 'mount' para cargar los datos iniciales
mount(function () {
    $this->customers = User::all();
});

$toggleStatus = action(function (User $user) {
    $user->status = !$user->status;
    $user->save();
    $this->customers = User::all();
});

// --- NUEVO: Acción para guardar el nuevo usuario ---
$save = action(function () {
    // Validación de los datos
    $validated = $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'employee_number' => 'nullable|string|unique:users,employee_number',
        'role_id' => 'required|integer',
        'password' => 'required|min:8|confirmed',
    ]);

    // Añadir el password hasheado
    $validated['password'] = Hash::make($this->password);

    // Crear el usuario
    User::create($validated);

    // Resetear el formulario, cerrar el modal y refrescar la lista
    $this->reset(['name', 'email', 'employee_number', 'role_id', 'password', 'password_confirmation']);
    $this->showModal = false;
    $this->customers = User::all();

    // Opcional: Enviar una notificación (se puede mostrar en la vista)
    session()->flash('success', 'Usuario creado exitosamente.');
});
// --- FIN NUEVO ---

// --- NUEVO: Función para cerrar el modal sin guardar ---
$closeModal = action(function () {
    $this->showModal = false;
});
// --- FIN NUEVO ---
?>

<div>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Lista de Clientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end mb-4">
                        <button wire:click="$set('showModal', true)" class="px-4 py-2 font-bold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            Crear Cliente
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Correo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                                @forelse ($customers as $customer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $customer->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $customer->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($customer->status == 1)
                                                <button wire:click="toggleStatus({{ $customer->id }})" class="px-3 py-1 text-sm font-semibold text-white bg-red-500 rounded-md hover:bg-red-600">
                                                    Baja
                                                </button>
                                            @else
                                                <button wire:click="toggleStatus({{ $customer->id }})" class="px-3 py-1 text-sm font-semibold text-white bg-green-500 rounded-md hover:bg-green-600">
                                                    Alta
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Este bloque se muestra si $customers está vacío --}}
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                            No se encontraron usuarios.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl p-6 mx-4 bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Crear Nuevo Cliente</h3>

            <form wire:submit="save">
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                        <input type="text" wire:model="name" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                        <input type="email" wire:model="email" id="email" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        @error('email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Empleado</label>
                        <input type="text" wire:model="employee_number" id="employee_number" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        @error('employee_number') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol ID</label>
                        <input type="number" wire:model="role_id" id="role_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        @error('role_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                        <input type="password" wire:model="password" id="password" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        @error('password') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                        <input type="password" wire:model="password_confirmation" id="password_confirmation" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700">
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 font-bold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 dark:text-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 font-bold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>