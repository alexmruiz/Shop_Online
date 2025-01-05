<div class="container mt-4">
    <x-card cardTitle="Confirmación de Pedido">
        <div class="card-body">
            <h3 class="text-success text-center">¡Gracias por tu pedido, {{ Auth::user()->name }}!</h3>
            <p class="text-center">Pedido número: <strong>{{ $cart->order_number }}</strong></p>

            <hr>

            <!-- Detalles del Pedido -->
            <h5 class="mt-4">Detalles del Pedido</h5>
            <ul class="list-group">
                @php
                    $total = 0;
                @endphp
                @foreach ($cart->cartItems as $item)
                    <li class="list-group-item d-flex align-items-center">
                        <div class="flex-shrink-0">
                            @php
                                $product = \App\Models\Product::find($item->product_id);
                            @endphp
                            @if($product) 
                                <x-image-product :product="$product" class="img-thumbnail me-3" /> 
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                            <p class="mb-0">
                                {{ $item->quantity }} unidades - 
                                <strong>{{ number_format($item->unit_price * $item->quantity, 2) }} €</strong>
                            </p>
                        </div>
                    </li>
                    @php
                        // Sumar el precio del artículo al total
                        $total += $item->unit_price * $item->quantity;
                    @endphp
                @endforeach
            </ul>

            <hr>

            <!-- Total de la compra -->
            <h5 class="mt-4">Total de la Compra</h5>
            <p class="text-end">
                <strong>{{ number_format($total, 2) }} €</strong>
            </p>

            <hr>

            <!-- Dirección de Envío -->
            <h5 class="mt-4">Dirección de Envío</h5>
            <p>
                <strong>{{ $cart->address }}</strong>
            </p>

            <hr>

            <!-- Información adicional -->
            <div class="mt-4">
                <p class="text-muted">
                    Nuestra agencia de reparto le comunicará el día de la entrega.
                </p>
                <p>
                    Puede revisar todos los detalles del pedido junto con su estado en 
                    <a href="#" class="text-primary text-decoration-underline">MIS PEDIDOS</a>.
                </p>
                <p>
                    Recuerde que dispone de <strong>30 días</strong> para realizar una devolución.
                </p>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="btn btn-primary">Volver al inicio</a>
            </div>
        </div>
    </x-card>
</div>
