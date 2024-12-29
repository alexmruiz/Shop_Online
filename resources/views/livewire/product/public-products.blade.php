<div class="mt-4">
    <x-card cardTitle="Sistema de Ventas">
        <!-- Barra de herramientas -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Buscador -->
            <div class="input-group w-50">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" wire:model.live='search' class="form-control" placeholder="Buscar...">
            </div>
            <!-- Filtro por categoría -->
            <div class="form-group">
                <select class="form-select" wire:model.live="selectedCategory">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
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
            @if ($products->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        Categoría sin productos
                    </div>
                </div>
            @else
                @foreach ($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow rounded-3">
                        <div class="row g-0">
                            <!-- Columna de imagen del producto -->
                            <div class="col-md-4 p-0">
                                <x-image-product :product="$product" class="card-img-top h-100 w-100 object-fit-cover" />
                            </div>
                
                            <!-- Columna de texto: Nombre, descripción y precio -->
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column text-center h-100">
                                    <h5 class="card-title text-dark fw-bold">{{ $product->name }}</h5>
                                    <p class="card-text text-muted">{{ $product->description }}</p>
                                    <p class="card-text font-weight-bold text-primary">{{ $product->price }} €</p>
                                    <div class="mt-auto">
                                        <a href="#" class="btn btn-primary w-100">
                                            <i class="fas fa-shopping-cart"></i> Añadir al carrito
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Paginación -->
        <x-slot:cardFooter>
            {{ $products->links() }}
        </x-slot:cardFooter>
    </x-card>
</div>
