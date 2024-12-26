<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;
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
     public $phone;
     public $address;
   
    public function render()
    {
        $this->totalRegistros = Client::count();
        $clients = Client::where('name', 'like', '%'.$this->search.'%')
        ->orderBy('id', 'desc') 
        ->paginate($this->cant);
        return view('livewire.client.client-component', [
            'clients' => $clients
        ]);
    }

    #[Computed()]
    public function categories()
    {
        return Category::all();
    }

    public function create(){

        $this->Id=0;

        $this->clean();
        
        $this->dispatch('open-modal', 'modalClient');
    }
    //Crear productos
    public function store(){
        
        $rules = [
            'name' => 'required|min:5|max:255|unique:clients,name', // El campo 'name' es obligatorio, tiene una longitud mínima de 5, máxima de 255 y debe ser único en la tabla 'clients'.
            'email' => 'nullable|email|max:255|unique:clients,email', // El campo 'email' es opcional, debe ser un correo válido y único.
            'phone' => 'required|numeric|digits_between:10,15', // El campo 'phone' es obligatorio, debe ser numérico y tener entre 10 y 15 dígitos.
            'address' => 'required|string|max:255', // El campo 'address' es obligatorio, debe ser una cadena de texto con un máximo de 255 caracteres.
        ];
        
        $messages = [
            'name.required' => 'El nombre del cliente es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre ya está registrado en nuestra base de datos.',
            
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado en nuestra base de datos.',
            
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.numeric' => 'El teléfono debe contener solo números.',
            'phone.digits_between' => 'El teléfono debe tener entre 10 y 15 dígitos.',
            
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser un texto válido.',
            'address.max' => 'La dirección no puede exceder los 255 caracteres.',
        ];
        
    
        $this->validate($rules, $messages);

        $client = new Client();
        
        $client->name = $this->name;
        $client->email = $this->email;
        $client->phone = $this->phone;
        $client->address = $this->address;
        $client->save();

        $this->dispatch('close-modal', 'modalClient');
        $this->dispatch('msg', 'Cliente creado correctamente');
        $this->clean();
    }

    public function edit(Client $client){

        $this->clean();
        $this->Id = $client->id;
        $this->name = $client->name;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->address = $client->address;

        $this->dispatch('open-modal', 'modalClient');
    }

    public function update(Client $client){
        
        $rules = [
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $client->id, 
            'phone' => 'required|numeric|min:9', 
            'address' => 'required|string|max:255',
        ];
    
        $messages = [
            'name.required' => 'El nombre del cliente es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado en nuestra base de datos.',
    
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.numeric' => 'El teléfono debe contener solo números.',
            'phone.min' => 'Minimo 9 números',
    
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser un texto válido.',
            'address.max' => 'La dirección no puede exceder los 255 caracteres.',
        ];

        $this->validate($rules, $messages);

        $client -> name = $this->name;
        $client -> email = $this->email;
        $client -> phone = $this->phone;
        $client -> address = $this->address;
        $client->update();

        $this->dispatch('close-modal', 'modalClient');
        $this->dispatch('msg', 'Cliente editado correctamente');

        $this->clean();
    }

    //Métdo encargado de la limpieza del modal
    public function clean()
    {
        $this->reset(['Id', 'name', 'email', 'phone', 
         'address']);
        $this->resetErrorBag();
    }

    #[On('destroyClient')]
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        $this->dispatch('msg', 'El cliente ha sido eliminado correctamente');
    }
}
