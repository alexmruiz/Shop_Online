<div class="mt-4 mb-4">
    <x-card-public cardTitle="{{ __('home.explore_products') }}">
        @auth
            <div class="alert alert-info" role="alert">
                <i class="bi bi-clock me-1"></i> {{ __('cart.expiration_notice') }}
            </div>
        @endauth

        <!-- Barra de herramientas -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <!-- Buscador -->
            <div class="input-group w-md-50 w-100 mb-4">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" wire:model.live='search' class="form-control" placeholder="{{ __('home.search_placeholder') }}">
            </div>
            <!-- Filtro por categoría -->
            <div class="form-group w-auto">
                <select class="form-select" wire:model.live="selectedCategory">
                    <option value="">{{ __('home.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Botón de carrito -->
            @auth
                <a href="{{ route('cart') }}"
                    wire:navigate
                    class="btn btn-warning position-relative rounded-pill px-4 py-2 shadow-lg w-auto">
                    <i class="bi bi-cart fs-5"></i> {{ __('home.shopping_cart') }}
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $this->total }}
                    </span>
                </a>
            @endauth
            <!-- Selector de resultados por página -->
            <div class="form-group w-auto">
                <select class="form-select" wire:model.live='cant'>
                    <option value="15">{{ __('home.results_15') }}</option>
                    <option value="21">{{ __('home.results_21') }}</option>
                    <option value="30">{{ __('home.results_30') }}</option>
                </select>
            </div>
        </div>

        <!-- Lista de productos -->
        <div class="row">
            @guest
                <h5 class="text-center fw-bold mt-4 mb-4">
                    {{ __('home.must_register') }}
                </h5>
            @endguest

            @if ($products->isEmpty())
                <div class="col-12">
                    <div class="alert alert-warning text-center w-100" role="alert">
                        {{ __('home.no_products') }}
                    </div>
                </div>
            @else
                @foreach ($products as $product)
                    <div class="col-md-4 col-sm-6 col-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <!-- Imagen del Producto -->
                            <x-image-product :product="$product" class="image-product" />

                           <!-- Detalles del Producto -->
                            <div class="card-body d-flex flex-column text-center">
                                <h5 class="card-title text-dark fw-bold">{{ $product->name }}</h5>
                                
                                <p class="card-text text-muted small">
                                    {{ Str::limit($product->description, 100, '...') }}
                                </p>

                                <p class="card-text fw-bold text-primary fs-5">{{ number_format($product->price, 2) }} €</p>

                                {{-- Indicadores de stock --}}
                                @if ($product['available_stock'] > 0 && $product['available_stock'] <= 5)
                                    <p class="card-text fw-bold text-danger small">
                                        <i class="bi bi-exclamation-triangle-fill"></i> ¡Solo quedan {{ $product['available_stock'] }} uds disponibles!
                                    </p>
                                @endif

                                <!-- Botón Añadir al Carrito -->
                                <div class="mt-auto">
                                    @auth
                                        @if ($product['available_stock'] > 0)
                                            <button type="button"
                                                    wire:click.prevent="addToCart({{ $product->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="btn btn-primary w-100 mt-2">
                                                <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                                    <i class="bi bi-cart-plus me-1"></i> {{ __('home.add_to_cart') }}
                                                </span>
                                                <span wire:loading wire:target="addToCart({{ $product->id }})">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </span>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary w-100 mt-2" disabled>
                                                <i class="bi bi-slash-circle me-1"></i> {{ __('home.out_of_stock') }}
                                            </button>
                                        @endif
                                    @endauth

                                    @guest
                                        @if ($product->stock > 0)
                                            <a href="{{ route('login') }}" class="btn btn-primary w-100 mt-2">
                                                <i class="bi bi-cart-plus me-1"></i> {{ __('home.add_to_cart') }}
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-secondary w-100 mt-2" disabled>
                                                <i class="bi bi-slash-circle me-1"></i> {{ __('home.out_of_stock') }}
                                            </button>
                                        @endif
                                    @endguest
                                </div>

                                <!-- Feedback Alpine.js al añadir -->
                                <div x-data="{ show: false, message: '' }"
                                    x-on:product-add.window="
                                        const eventDetail = Array.isArray($event.detail) ? $event.detail[0] : $event.detail;
                                        if (eventDetail.id == {{ $product->id }}) {
                                            show = true; 
                                            message = '{{ __("home.added_to_cart", ["product" => $product->name]) }}'; 
                                            setTimeout(() => show = false, 3000);
                                        }
                                    "
                                    class="mt-2">
                                    <div x-show="show" 
                                        x-transition 
                                        x-cloak 
                                        class="alert alert-success small py-1 px-2 mb-0" 
                                        role="alert">
                                        <i class="bi bi-check-circle-fill me-1"></i> <span x-text="message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </x-card-public>
</div>
