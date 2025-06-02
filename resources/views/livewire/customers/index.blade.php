<?php

use function Livewire\Volt\state;
use function Livewire\Volt\on;
use function Livewire\Volt\action; // Es buena práctica usar action() para los métodos
use App\Models\User;

// Sintaxis correcta para 'state': state('nombreVariable', valorInicial);
state('customers', []);
state('lastUpdated', null);

on([
    'mount' => function () {
        $this->customers = User::all();
        $this->lastUpdated = now()->toTimeString();
    },
]);

$refresh = action(function () { // Envolvemos la acción
    $this->customers = User::all();
    $this->lastUpdated = now()->toTimeString();
});

?>

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Lista de Clientes') }}
            </h2>
            {{-- Mostramos la hora de la última actualización --}}
            <span class="text-sm text-gray-500">
                Última Actualización: {{ $lastUpdated }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- ESTE ES NUESTRO BOTÓN "AJAX" --}}
                    <div class="mb-4">
                        <button wire:click="refresh"
                            class="px-4 py-2 font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                            Recargar Lista
                        </button>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        {{-- ... la tabla se queda exactamente igual ... --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Nombre
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Email
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                            @foreach ($customers as $customer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $customer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $customer->email }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
