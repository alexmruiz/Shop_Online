<div>
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
                            <div class="position-relative">
                                <x-image-product :product="$product" class="image-product" />

                                    <button type="button"
                                            wire:click="toggleFavorite({{ $product->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="toggleFavorite({{ $product->id }})"
                                            class="btn btn-light text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm"
                                            aria-label="Quitar de favoritos"
                                            aria-pressed="true">
                                        <i class="bi bi-bookmark-heart-fill"></i>
                                    </button>
                            </div>

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
</div>
