<div class="mt-4">
    <x-card cardTitle="Cesta de la compra">
        <x-slot:cardTools>
            <a href="{{ route('home') }}" class="btn btn-outline-primary float-end">
                <i class="bi bi-arrow-left-circle"></i> Regresar
            </a>
        </x-slot>

        <div class="p-4">
            <h2 class="text-lg font-bold mb-4">Productos añadidos</h2>
            @if(empty($cartItems))
                <div class="alert alert-info" role="alert">
                    NO HAS AÑADIDO ARTÍCULOS A LA CESTA
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
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $item['name'] }}</h5>
                                <small class="text-muted">Cantidad: {{ $item['quantity'] }}</small><br>
                                <small class="text-muted">Precio Unitario: ${{ number_format($item['price'], 2) }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <button wire:click="decreaseQuantity('{{ $item['id'] }}')" class="btn btn-sm btn-outline-secondary me-2">-</button>
                                <span>{{ $item['quantity'] }}</span>
                                <button wire:click="increaseQuantity('{{ $item['id'] }}')" class="btn btn-sm btn-outline-secondary ms-2">+</button>
                            </div>
                            <button wire:click="removeFromCart('{{ $item['id'] }}')" class="btn btn-sm btn-outline-danger ms-3">
                                <i class="bi bi-trash"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="text-lg font-bold">Total: {{ number_format($total, 2) }}€</h3>
                    <button wire:click="checkout" class="btn btn-primary">
                        <i class="bi bi-credit-card"></i> Proceder al pago
                    </button>
                </div>
            @endif
        </div>

        <x-slot:cardFooter>
            <p class="text-muted text-center mb-0">Gracias por confiar en nosotros.</p>
        </x-slot>
    </x-card>
</div>
