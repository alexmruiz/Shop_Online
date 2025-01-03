<div class="mt-4 mb-4">
        
    <x-card cardTitle="Explora nuestros productos">
        <!-- Barra de herramientas -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Buscador -->
            <div class="input-group w-50">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" wire:model.live='search' class="form-control" placeholder="Buscar...">
            </div>
            
            <!-- Botón de carrito -->
            @auth
            <a href="{{ route('cart') }}" class="btn btn-warning position-relative rounded-pill px-4 py-2 shadow-lg" aria-label="Ver carrito">
                <i class="bi bi-cart fs-5"></i> Cesta de la compra
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{$this->total}}
                </span>
            </a>
            @endauth
            
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
        <div>
            @if ($feedbackMessage)
                <div 
                    x-data="{ show: true }"
                    x-show="show"
                    id="flash-message"
                    x-init="setTimeout(() => show = false, 6000); 
                             $wire.set('feedbackMessage', null)"
                    class="alert alert-success text-center fw-bold position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg"
                    style="z-index: 1050; max-width: 90%;">
                    {{ $feedbackMessage }}
                </div>
            @endif
        </div>
        
        
        <!-- Lista de productos -->
        <div class="row">
            @guest
                <h5 class="text-center fw-bold mt-4 mb-4">Para poder comprar es necesario registrarse</h5>
            @endguest

            @if ($products->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        Categoría sin productos
                    </div>
                </div>
            @else
                @foreach ($products as $product)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <!-- Imagen del Producto -->
                            <x-image-product :product="$product" class="card-img-top w-100 h-100 object-fit-cover"
                                alt="Imagen de {{ $product->name }}" />

                            <!-- Detalles del Producto -->
                            <div class="card-body d-flex flex-column text-center">
                                <h5 class="card-title text-dark fw-bold">{{ $product->name }}</h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($product->description, 100, '...') }}</p>
                                <p class="card-text fw-bold text-primary">{{ number_format($product->price, 2) }} €</p>

                                <!-- Botón Añadir al Carrito -->
                                @auth
                                    <div class="mt-auto">
                                        <a href="#" wire:click.prevent="addToCart('{{ $product->id }}')"
                                            class="btn btn-primary w-100">
                                            <i class="fas fa-shopping-cart"></i> Añadir al carrito
                                        </a>
                                    </div>
                                @endauth
                                @guest
                                <div class="mt-auto">
                                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                        <i class="fas fa-shopping-cart"></i> Añadir al carrito
                                    </a>
                                </div>
                                @endguest                                 
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
