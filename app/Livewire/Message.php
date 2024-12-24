<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

/**
 * Componente para gestionar mensajes de usuario.
 * Esta clase permite manejar eventos de mensajes
 * y mostrarlos como mensajes flash en la sesión actual.
 */
class Message extends Component
{
    /**
     * Renderiza la vista asociada al componente.
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Devuelve la vista correspondiente al componente Livewire
        return view('livewire.message');
    }

    /**
     * Maneja el evento 'msg' para mostrar mensajes flash.
     * 
     * @param string $msg El mensaje que se mostrará en la sesión flash.
     * @return void
     */
    #[On('msg')]
    public function msgs($msg)
    {
        // Almacena el mensaje recibido en la sesión flash
        session()->flash('msg', $msg);
    }
}
