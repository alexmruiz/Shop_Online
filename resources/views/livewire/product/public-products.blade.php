<div class="mt-4">
    <x-card cardTitle="Sistema de Ventas">
        <!-- Barra de herramientas -->
        <div class="d-flex justify-content-between align-items-center mb-4">
  
            <!-- Buscador -->
            <div class="input-group w-50">
                <input type="text" wire:model.live='search' class="form-control" placeholder="Buscar...">   
            </div>

           <!-- Filtro por categoría -->
            <div class="form-group">
                <select class="form-select" wire:model.live="selectedCategory">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selector de resultados por página -->
            <div class="form-group">
                <select class="form-select" wire:model.live='cant'>
                    <option value="15">15 resultados</option>
                    <option value="21">21 resultados</option>
                    <option value="30">30 resultados</option>
                </select>
            </div>
        </div>

        <!-- Lista de productos -->
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        {{-- <img src="path/to/product-image.jpg" class="card-img-top" alt="{{ $product->name }}"> --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ $product->description }}</p>
                            <p class="card-text font-weight-bold">{{ $product->price }} €</p>
                            <div class="mt-auto">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Añadir al carrito
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        

        <!-- Paginación -->
        <x-slot:cardFooter>
            {{$products->links()}}
        </x-slot>
    </x-card>
</div>
