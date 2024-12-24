<div>
    <x-card cardTitle="Listado Categorias ({{$this->totalRegistros}})">
        <x-slot:cardTools>
            <a href="#" class="btn btn-primary" wire:click='create'>
                <i class="fas fa-plus-circle"></i>
                Crear categoria              
            </a>
        </x-slot:cardTools>
       
        <x-table>
            <x-slot:thead> 
                <th>Id</th>
                <th>Nombre</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
            </x-slot:thead>

            @forelse ($categories as $category)
                
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="{{ route('categoryShow',$category)}} " class="btn btn-success bt-sm" title="Ver">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#" wire:click="edit({{$category->id}})" class="btn btn-primary bt-sm" title="Editar">
                                <i class="far fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <a wire:click="$dispatch('delete', {id: {{$category->id}}, eventName: 'destroyCategory'})"
                                class="btn btn-danger bt-sm" title="Eliminar">
                                <i class="far fa-trash-alt"></i>
                             </a>
                             
                        </td>
                    </tr>
                
            @empty
                
                    <tr class="text-center">
                        <td colspan="5">Sin registros</td>
                    </tr>
                
            @endforelse
        </x-table>
        <x-slot:cardFooter>
            {{$categories->links()}}
        </x-slot>
    </x-card>

    <x-modal modalId="modalCategory" modalTitle="Categorías">
        <form wire:submit.prevent="{{$Id==0 ? "store" : "update($Id)"}} ">
            <div class="form-row">
                <div class="form-group col-12">
                    <label for="name">Nombre:</label>
                    <input wire:model='name' type="text" class="form-control" id="name" placeholder="Nombre de la categoria">
                    @error('name')
                        <div class="alert alert-danger w-100 mt-2">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <hr>
            <button class="btn btn-primary float-right">{{$Id==0 ? 'Guardar' : 'Editar'}}</button>
        </form>
    </x-modal>
</div>

