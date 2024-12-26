<div>
    <x-card cardTitle="Listado de Clientes ({{$this->totalRegistros}})">
        <x-slot:cardTools>
            <a href="#" class="btn btn-primary" wire:click='create'>
                <i class="fas fa-plus-circle"></i>
                Crear Cliente              
            </a>
        </x-slot>
       
        <x-table>
            <x-slot:thead> 
                <th>Id</th>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
            </x-slot>

            @forelse ($clients as $client)
                
                    <tr>
                        <td>{{ $client->id }}</td>

                        <td>{{ $client->name }}</td>

                        <td>{{ $client->email }}</td>

                        <td>{{ $client->phone }}</td>

                        <td>{{ $client->address }}</td>

                                         
                        <td>
                            <a href="{{route('productShow',$client)}}" class="btn btn-success bt-sm" title="Ver">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#" wire:click="edit({{$client->id}})" class="btn btn-primary bt-sm" title="Editar">
                                <i class="far fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <a wire:click="$dispatch('delete', {id: {{$client->id}}, eventName: 'destroyClient'})"
                                class="btn btn-danger bt-sm" title="Eliminar">
                                <i class="far fa-trash-alt"></i>
                             </a>
                             
                        </td>
                    </tr>
                
            @empty
                
                    <tr class="text-center">
                        <td colspan="10">Sin registros</td>
                    </tr>
                
            @endforelse
        </x-table>
        <x-slot:cardFooter>
            {{$clients->links()}}
        </x-slot>
    </x-card>
@include('components.modal_client')
</div>


