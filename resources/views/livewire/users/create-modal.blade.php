<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="w-full max-w-2xl p-6 mx-4 bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <form wire:submit="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Crear Nuevo Usuario
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Rellena los campos para registrar un nuevo usuario en el sistema.
            </p>

            <div class="mt-6 space-y-4">
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nombre</label>
                    <input wire:model.defer="name" id="name" type="text" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="employee_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Número de Empleado</label>
                    <input wire:model.defer="employee_number" id="employee_number" type="text" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    @error('employee_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                    <input wire:model.defer="email" id="email" type="email" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="role_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Rol</label>
                    <select wire:model.defer="role_id" id="role_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Selecciona un rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Contraseña</label>
                    <input wire:model.defer="password" id="password" type="password" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" wire:click="cancel" class="mr-3 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>