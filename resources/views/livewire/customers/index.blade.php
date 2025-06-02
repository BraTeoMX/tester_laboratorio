<?php

use function Livewire\Volt\state;
use function Livewire\Volt\mount; // 1. Importa la función 'mount'
use function Livewire\Volt\action;
use App\Models\User;

state('customers', []);

// 2. Usa la función 'mount' para cargar los datos iniciales
mount(function () {
    $this->customers = User::all();
});

$toggleStatus = action(function (User $user) {
    $user->status = !$user->status;
    $user->save();
    $this->customers = User::all();
});

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
</div>