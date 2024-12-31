<div>
    <x-card cardTitle="Listado de Clientes ({{$this->totalRegistros}})">
        <x-slot:cardTools>
             <!-- Filtro por rol -->
            <a href="#" class="btn btn-primary" wire:click='create'>
                <i class="fas fa-plus-circle"></i>
                Crear Usuario              
            </a>            
        </x-slot>  
        <x-table>
            <div class="form-group col-md-3">
                <select class="form-select" wire:model.live="selectedUser">
                    <option value="">Todos los usuarios</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}">{{ $role }}</option>
                    @endforeach
                </select>
            </div>    
            <x-slot:thead> 
                <th>Id</th>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Rol</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
            </x-slot>

            @forelse ($users as $user)
                
                    <tr>
                        <td>{{ $user->id }}</td>

                        <td>{{ $user->name }}</td>

                        <td>{{ $user->email }}</td>

                        <td>{{ $user->role }}</td>
                                         
                       {{--   <td>
                            <a href="{{route('productShow',$user)}}" class="btn btn-success bt-sm" title="Ver">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>--}}
                        <td>
                            <a href="#" wire:click="edit({{$user->id}})" class="btn btn-primary bt-sm" title="Editar">
                                <i class="far fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <a wire:click="$dispatch('delete', {id: {{$user->id}}, eventName: 'destroyClient'})"
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
            {{$users->links()}}
        </x-slot>
    </x-card>
@include('components.modal_client')
</div>


