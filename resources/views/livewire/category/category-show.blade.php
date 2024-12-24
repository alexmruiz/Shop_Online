<x-card cardTitle="Detalles de la categoria">
    <x-slot:cardTools>
        <a href="{{ route('category')}}" class="btn btn-primary">
           <i class="fas fa-arrow-circle-left"></i> Regresar
        </a>
    </x-slot>

    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">

                    <h2 class="profile-username text-center">{{$category->name}}</h2>
                    <ul class="list-group  mb-3">
                        <li class="list-group-item">
                            <b>Productos</b> <a class="float-right">{{count($category->products)}}</a>
                        </li>
                    </ul>
                </div>
            
            </div>
        </div>
        @if ($category->products->isNotEmpty())
        <div class="col-md-8">
            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($category->products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{!! $product->price !!}</td>
                        </tr>
                        @empty
                        <tr class="text-center">
                            <td colspan="3">Sin registros</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

</x-card>
