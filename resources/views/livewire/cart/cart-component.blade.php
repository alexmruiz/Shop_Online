<div class="mt-4">
    <x-card-public cardTitle="{{ __('cart.cart_title') }}">
        <x-slot:cardTools>
            <a href="{{ route('home') }}" class="btn btn-outline-primary float-end">
                <i class="bi bi-arrow-left-circle"></i> {{ __('cart.back') }}
            </a>
        </x-slot>

        <div class="p-4">
            <h2 class="text-lg font-bold mb-4">{{ __('cart.added_products') }}</h2>
            @if(empty($cartItems))
                <div class="alert alert-info" role="alert">
                    {{ __('cart.empty_cart') }}
                </div>
            @else
                <ul class="list-group mb-4">
                    @foreach ($cartItems as $item)
                        <li class="list-group-item d-flex align-items-center">
                            <!-- Componente de imagen -->
                            @php 
                                $product = \App\Models\Product::find($item['product_id']); 
                            @endphp
                            
                            @if($product) 
                                <x-image-product :product="$product" class="img-thumbnail me-3" /> 
                            @endif

                            <!-- Información del producto -->
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $item['name'] }}</h5>
                                <small class="text-muted">{{ __('cart.quantity') }}: {{ $item['quantity'] }}</small><br>
                                <small class="text-muted">{{ __('cart.unit_price') }}: {{ number_format($item['price'], 2) }}€</small>
                            </div>

                            <!-- Botones para modificar la cantidad -->
                            <div class="d-flex align-items-center">
                                <button wire:click="decreaseQuantity('{{ $item['id'] }}')" class="btn btn-sm btn-outline-secondary me-2">-</button>
                                <span>{{ $item['quantity'] }}</span>
                                <button wire:click="increaseQuantity('{{ $item['id'] }}')" class="btn btn-sm btn-outline-secondary ms-2">+</button>
                            </div>

                            <!-- Botón para eliminar -->
                            <button wire:click="removeFromCart('{{ $item['id'] }}')" class="btn btn-sm btn-outline-danger ms-3">
                                <i class="bi bi-trash"></i> {{ __('cart.remove') }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="text-lg font-bold">{{ __('cart.total') }}: {{ number_format($total, 2) }}€</h3>
                    <a href="{{ route('checkout') }}" wire:navigate class="btn btn-primary">
                        <i class="bi bi-credit-card"></i> {{ __('cart.checkout') }}
                    </a>                    
                </div>
            @endif
        </div>

        <x-slot:cardFooter>
            <p class="text-muted text-center mb-0">{{ __('cart.thanks') }}</p>
        </x-slot>
    </x-card-public>
</div>
