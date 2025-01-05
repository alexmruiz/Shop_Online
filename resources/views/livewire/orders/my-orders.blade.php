<div class="container mt-4">
    <x-card cardTitle="Mis Pedidos">
        <div class="card-body">
            <h3 class="text-primary">Historial de Pedidos</h3>
            
            @foreach($carts as $cart)
                <div class="cart-item mt-4 border rounded p-3 shadow-sm">
                    <h5 class="mb-3">Número de Pedido: <strong>{{ $cart->order_number }}</strong></h5>
                    <p>Estado: <strong>{{ ucfirst($cart->status) }}</strong></p>
                    <p>Fecha de realización: <strong>{{ $cart->formatted_date }}</strong></p>
                    <p>Dirección: <strong>{{ $cart->address }}</strong></p>
                    <p>Total: <strong>{{ $cart->total }}€</strong></p>

                    <h6 class="mt-3">Productos en este pedido:</h6>
                    <ul class="list-unstyled">
                        @foreach($cart->cartItems as $item)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $item->product->name }} - {{ $item->quantity }} unidades</span>
                                    <strong>{{ $item->unit_price * $item->quantity }}€</strong>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if($carts->isEmpty())
                <p class="mt-4">Aún no has realizado ningún pedido.</p>
            @endif
        </div>
    </x-card>
</div>
