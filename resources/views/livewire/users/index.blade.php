<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\CatalogoRol;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
// use Livewire\Attributes\Rule; // Elimina o comenta esta línea si la tienes
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


new class extends Component {
    use WithPagination;

    public bool $isModalOpen = false;

    // 🆕 1. NUEVA PROPIEDAD PARA EL CHECKBOX
    public bool $generateEmail = false;

    // ⬇️ 2. PROPIEDADES SIN REGLAS DIRECTAS (se moverán a un método)
    public $name = '';
    public $employee_number = '';
    public $email = '';
    public $role_id = '';
    public $password = '';

    protected $listeners = [
        'toggleStatusConfirmed'
    ];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'employee_number' => 'required|string|max:255|unique:users,employee_number',
            // El email es requerido SÓLO si el checkbox no está marcado
            'email' => [
                Rule::requiredIf(!$this->generateEmail),
                'nullable', // Permite que sea nulo si el checkbox está marcado
                'email',
                'max:255',
                'unique:users,email'
            ],
            'role_id' => 'required|exists:catalogo_roles,id',
            'password' => 'required|string|min:8',
        ];
    }


    public function resetInputFields(): void
    {
        // Añadimos la nueva propiedad y el resto de campos
        $this->reset(['name', 'employee_number', 'email', 'role_id', 'password', 'generateEmail']);
        $this->resetValidation(); // Limpia los errores de validación anteriores
    }

    public function openModal(): void
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function with(): array
    {
        return [
            'users' => User::with('role')->orderBy('name')->paginate(10),
            'roles' => CatalogoRol::all(),
        ];
    }

    public function saveUser(): void
    {
        $validatedData = $this->validate();

        // Preparamos los datos para la creación
        $userData = [
            'name' => $validatedData['name'],
            'employee_number' => $validatedData['employee_number'],
            'role_id' => $validatedData['role_id'],
            'password' => Hash::make($validatedData['password']),
        ];

        // Si el checkbox NO está marcado, incluimos el email proporcionado
        if (!$this->generateEmail) {
            $userData['email'] = $validatedData['email'];
        }

        // 1. Creamos el usuario (con o sin email)
        $user = User::create($userData);

        // 2. Si el checkbox SÍ estaba marcado, generamos y guardamos el email
        if ($this->generateEmail) {
            $user->email = 'lab' . $user->id . '@lab.com';
            $user->save(); // 3. Actualizamos el registro
        }
        
        $this->closeModal();

        LivewireAlert::title('¡Hecho!')
            ->text('Usuario creado correctamente.')
            ->success()
            ->toast()
            ->position('top-end')
            ->timer(3000)
            ->show();
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
                
                <form wire:submit.prevent="saveUser">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Crear Nuevo Usuario
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                            <input type="text" id="name" wire:model="name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Empleado</label>
                            <input type="text" id="employee_number" wire:model="employee_number" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @error('employee_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                            <div class="flex items-center space-x-2 mt-1">
                                <input type="email" id="email" wire:model="email"
                                    @disabled($generateEmail) 
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm disabled:bg-gray-200 dark:disabled:bg-gray-700 disabled:cursor-not-allowed">
                                
                                <div class="flex items-center">
                                    <input id="generateEmail" type="checkbox" wire:model.live="generateEmail" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="generateEmail" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Autogenerar</label>
                                </div>
                            </div>
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                            <select id="role_id" wire:model="role_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                            <input type="password" id="password" wire:model="password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Guardar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>