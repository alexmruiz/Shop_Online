<div class="container mt-4 mb-4">
    <x-card cardTitle="Detalles del Cliente">
        <div class="card-body">
            <!-- Información del Cliente -->
            <h3 class="text-primary border-bottom pb-2">Cliente: {{ $user->name }}</h3>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role ?? 'Sin especificar' }}</p>
            <p><strong>Fecha de Registro:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </x-card>

    <x-card cardTitle="Pedidos Realizados" class="mt-4">
        @forelse($user->carts as $cart)
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
                    <span class="text-secondary">Fecha de Realización:</span>
                    <strong class="text-dark">{{ $cart->created_at->format('d/m/Y') }}</strong>
                </p>
                <p>
                    <span class="text-secondary">Dirección:</span>
                    <strong class="text-dark">{{ $cart->address }}</strong>
                </p>
                <p>
                    <span class="text-secondary">Total:</span>
                    <strong class="text-success fs-5">{{ $cart->cartItems->sum(fn($item) => $item->quantity * $item->unit_price) }}€</strong>
                </p>

                <!-- Productos del Pedido -->
                <h6 class="mt-4 text-primary">Productos en este pedido:</h6>
                <ul class="list-group list-group-flush mt-3">
                    @foreach($cart->cartItems as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-white">
                                <i class="bi bi-bag-fill me-2 text-secondary"></i>
                                {{ $item->product->name }} -
                                <span class="text-secondary">{{ $item->quantity }} unidades</span>
                            </span>
                            <strong class="text-white">{{ $item->quantity * $item->unit_price }}€</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="alert alert-info mt-4" role="alert">
                Este cliente no ha realizado pedidos.
            </div>
        @endforelse
    </x-card>
</div>
