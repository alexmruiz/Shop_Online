<?php

namespace App\Livewire\Client;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Title('Clientes')]
class ClientComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
   
    //Propiedades de la clase
    public $totalRegistros=0;
    public $search='';
    public $cant=5;
    //Propiedades modelo
    public $Id=0; 
    public $name;
    public $email;
    public $password;
    public $confirmpassword;
    public $role;

    public $selectedUser;
   
    public function render()
    {
        $query = User::query();

        if (!empty($this->selectedUser)) {
            $query->where('role', $this->selectedUser);
        }
        
        $this->totalRegistros = $query->count();
        
        $users = $query
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate($this->cant);

            $roles = User::select('role')->distinct()->pluck('role');
            return view('livewire.client.client-component', [
                'users' => $users,
                'roles' => $roles,
            ]);
    }

    public function create(){

        $this->Id=0;

        $this->clean();
        
        $this->dispatch('open-modal', 'modalClient');
    }
    //Crear productos
    public function store(){
        
        $rules = [
            'name' => 'required|min:5|max:255', 
            'email' => 'required|email|max:255|unique:users,email', 
            'password' => 'required|min:6', 
            'confirmpassword' => 'required|same:password',
            'role' => 'required', 
        ];       
        $messages = [
            'name.required' => 'El nombre del usuario es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre ya está registrado en nuestra base de datos.',
            
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado en nuestra base de datos.',
            
            'password.required' => 'La contraseña es obligatoria',
            'password.min'=> 'La contraseña debe de contener 6 caracteres mínimo',

            'confirmpassword.required' => 'La confirmación de contraseña es obligatoria.',
            'confirmpassword.same' => 'La confirmación de contraseña debe coincidir con la contraseña.',
            
            'role.required' => 'Debes elegir un rol.',
        ];
        
    
        $this->validate($rules, $messages);

        $user = new User();
        
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = bcrypt($this->password);
        $user->role = $this->role;
        $user->save();

        $this->dispatch('close-modal', 'modalClient');
        $this->dispatch('msg', 'Cliente creado correctamente');
        $this->clean();
    }

    public function edit(User $user){

        $this->clean();
        $this->Id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = $user->password;
        $this->confirmpassword = $user->password;
        $this->role = $user->role;

        $this->dispatch('open-modal', 'modalClient');
    }

    public function update(User $user)
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'email' => "required|email|max:255", // Excluye al usuario actual
            'password' => 'required|min:6', // Contraseña opcional durante la edición
            'confirmpassword' => 'required|same:password', // Opcional, pero debe coincidir si se proporciona
            'role' => 'required|string|max:255',
        ];
        //unique:users,email,{$user->id}
    
        $messages = [
            'name.required' => 'El nombre del usuario es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre ya está registrado en nuestra base de datos.',
    
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado en nuestra base de datos.',
            
            'password.require' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe contener al menos 6 caracteres.',
            'confirmpassword.same' => 'La confirmación de contraseña no coincide con la nueva contraseña.',
    
            'role.required' => 'Debes elegir un rol.',
        ];
    
        $this->validate($rules, $messages);
    
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);
    
        $this->dispatch('close-modal', 'modalClient');
        $this->dispatch('msg', 'Cliente editado correctamente');
    
        $this->clean();
    }
    
    //Métdo encargado de la limpieza del modal
    public function clean()
    {
        $this->reset(['Id', 'name', 'email', 'password', 'confirmpassword' ,
         'role']);
        $this->resetErrorBag();
    }

    #[On('destroyClient')]
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->dispatch('msg', 'El cliente ha sido eliminado correctamente');
    }
}
