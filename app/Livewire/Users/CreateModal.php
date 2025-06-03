<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\CatalogoRol;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use LivewireUI\Modal\ModalComponent;

class CreateModal extends ModalComponent
{
    public $name = '';
    public $employee_number = '';
    public $email = '';
    public $role_id = '';
    public $password = '';
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'employee_number' => 'required|string|unique:users,employee_number',
            'email' => 'required|email|max:255|unique:users,email',
            'role_id' => 'required|exists:catalogo_roles,id',
            'password' => 'required|string|min:8',
        ];
    }

    // Método de guardado SIMPLIFICADO
    public function save()
    {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'employee_number' => $this->employee_number,
                'email' => $this->email,
                'role_id' => $this->role_id,
                'password' => Hash::make($this->password),
            ]);
            
            LivewireAlert::title('¡Usuario Creado!')
                ->text('El nuevo usuario ha sido registrado correctamente.')
                ->success()->show();

            $this->closeModal();
            $this->dispatch('user-created');

        } catch (\Exception $e) {
            LivewireAlert::title('Error')
                ->text('No se pudo crear el usuario.')
                ->error()->show();
        }
    }
    
    public function render()
    {
        return view('livewire.users.create-modal', [
            'roles' => CatalogoRol::all()
        ]);
    }

    public function cancel()
    {
        $this->closeModal(); // Este método es provisto por ModalComponent
    }
}