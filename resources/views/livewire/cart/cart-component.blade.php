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
                    <li class="list-group-item d-flex align-items-center" wire:key="cart-item-{{ $item['id'] }}">
                        @php
                        $product = \App\Models\Product::find($item['product_id']);
                        @endphp

                        @if($product)
                        <x-image-product :product="$product" class="img-thumbnail me-3" />
                        @endif

                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $item['name'] }}</h5>
                            <small class="text-muted">{{ __('cart.quantity') }}: {{ $item['quantity'] }}</small><br>
                            <small class="text-muted">{{ __('cart.unit_price') }}: {{ number_format($item['price'], 2) }}€</small><br>

                            @if(!empty($item['reserved_until']))
                            <small
                                class="reservation-timer fw-bold"
                                data-reserved-until="{{ $item['reserved_until'] }}">
                                {{ __('cart.calculating') }}
                            </small>
                            @endif
                        </div>

                        <div class="d-flex align-items-center">
                            <button wire:click="decreaseQuantity('{{ $item['id'] }}')" class="btn btn-sm btn-outline-secondary me-2">-</button>
                            <span>{{ $item['quantity'] }}</span>
                            <button wire:click="increaseQuantity('{{ $item['id'] }}')" class="btn btn-sm btn-outline-secondary ms-2">+</button>
                        </div>

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

@push('scripts')
<script>
    function initCartTimers() {
        document.querySelectorAll('.reservation-timer').forEach(el => {
            if (el.dataset.currentUntil !== el.dataset.reservedUntil) {
                el.dataset.timerInitialized = '';
                el.dataset.currentUntil = el.dataset.reservedUntil;
            }

            if (el.dataset.timerInitialized) return;
            el.dataset.timerInitialized = 'true';

            let expiresAt = new Date(el.dataset.reservedUntil).getTime();

            function tick() {
                if (!document.body.contains(el)) return;

                // Actualiza expiresAt dinámicamente si cambia
                expiresAt = new Date(el.dataset.reservedUntil).getTime();

                const remaining = expiresAt - Date.now();

                if (remaining <= 0) {
                    el.textContent = '{{ __('cart.reservation_expired') }}';
                    el.classList.remove('text-warning');
                    el.classList.add('text-danger');
                    return; // dejamos de programar más ticks
                }

                const totalSeconds = Math.floor(remaining / 1000);
                const min = Math.floor(totalSeconds / 60);
                const sec = totalSeconds % 60;

                el.textContent = '{{ __('cart.time_left') }}: ' + min + ':' + String(sec).padStart(2, '0');

                if (remaining < 5 * 60 * 1000) {
                    el.classList.add('text-warning');
                }

                setTimeout(tick, 1000);
            }

            tick();
        });
    }

    document.addEventListener('DOMContentLoaded', initCartTimers);
    document.addEventListener('livewire:navigated', initCartTimers);
    // re-inicializa los timers de items nuevos tras cualquier actualización de Livewire (ej. increaseQuantity)
    document.addEventListener('livewire:updated', initCartTimers);
</script>
@endpush