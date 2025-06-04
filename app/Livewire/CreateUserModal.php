<?php

namespace App\Livewire;

// 1. ✅ Importa y extiende de ModalComponent
use LivewireUI\Modal\ModalComponent;


class CreateUserModal extends ModalComponent
{
    /**
     * 2. ✅ El método render() simplemente muestra la vista del modal.
     */
    public function render()
    {
        return view('livewire.create-user-modal');
    }

    /**
     * 3. (Opcional pero recomendado) ✅ Define un ancho para el modal.
     * Puedes usar 'sm', 'md', 'lg', 'xl', '2xl', etc.
     */
    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
}