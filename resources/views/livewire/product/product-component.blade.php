<div>
    <x-card cardTitle="Listado de Productos ({{$this->totalRegistros}})">
        <x-slot:cardTools>
            <a href="#" class="btn btn-primary" wire:click='create'>
                <i class="fas fa-plus-circle"></i>
                Crear producto              
            </a>
        </x-slot>
       
        <x-table>
            <x-slot:thead> 
                <th>Id</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Categoria</th>
                <th>Estado</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
                <th width="3%">...</th>
            </x-slot>

            @forelse ($products as $product)
                
                    <tr>
                        <td>{{ $product->id }}</td>

                        <td>{{ $product->name }}</td>

                        <td>{{ number_format($product->price, 2, ',', '.') }}€</td>

                        <td>
                            <a class="badge badge-secondary" href="{{route('categoryShow', $product->category->id)}}"> {{ $product->category->name }}
                        </td>
                        <td>{!! $product->activeLabel !!}</td>                          
                        <td>
                            <a href="{{route('productShow',$product)}}" class="btn btn-success bt-sm" title="Ver">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#" wire:click="edit({{$product->id}})" class="btn btn-primary bt-sm" title="Editar">
                                <i class="far fa-edit"></i>
                            </a>
                        </td>
                        <td>
                            <a wire:click="$dispatch('delete', {id: {{$product->id}}, eventName: 'destroyProduct'})"
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
            {{$products->links()}}
        </x-slot>
    </x-card>
@include('components.modal_products')
</div>


