<div class="container mt-4">
    <x-card-public cardTitle="Mis Pedidos">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Buscador -->
            <div class="input-group w-50">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" wire:model.live='search' class="form-control" placeholder="Buscar...">
            </div>

            <!-- Selector de resultados por página -->
            <div class="form-group">
                <select class="form-select" wire:model.live='cant'>
                    <option value="5">5 resultados</option>
                    <option value="10">10 resultados</option>
                    <option value="15">15 resultados</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <h3 class="text-primary border-bottom pb-2 mb-4">Historial de Pedidos</h3>

            @foreach($carts as $cart)
                <div class="cart-item mt-4 p-4 shadow-lg rounded-3 bg-light">
                    <h5 class="mb-3">
                        <span class="text-secondary">Número de Pedido:</span> 
                        <strong class="text-dark">{{ $cart->order_number }}</strong>
                    </h5>
                    <p>
                        <span class="text-secondary">Estado:</span> 
                        <strong class="badge {{ $cart->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($cart->status) }}
                        </strong>
                    </p>
                    <p>
                        <span class="text-secondary">Fecha de realización:</span> 
                        <strong class="text-dark">{{ $cart->formatted_date }}</strong>
                    </p>
                    <p>
                        <span class="text-secondary">Dirección de envio:</span> 
                        <strong class="text-dark">{{ $cart->address }}</strong>
                    </p>
                    <p>
                        <span class="text-secondary">Total:</span> 
                        <strong class="text-success fs-5">{{ $cart->total }}€</strong>
                    </p>

                    <h6 class="mt-4 text-primary">Productos en este pedido:</h6>
                    <ul class="list-group list-group-flush mt-3">
                        @foreach($cart->cartItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-bag-fill me-2 text-secondary"></i>
                                    {{ $item->product->name }} - 
                                    <span class="text-secondary">{{ $item->quantity }} unidades</span>
                                </span>
                                <strong class="text-dark">{{ $item->unit_price * $item->quantity }}€</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if($carts->isEmpty())
                <div class="alert alert-info mt-4" role="alert">
                    Aún no has realizado ningún pedido.
                </div>
            @endif
        </div>

        <x-slot:cardFooter>
            <!-- Pagina los resultados correctamente -->
            {{ $carts->links() }}
        </x-slot>
    </x-card>
</div>
