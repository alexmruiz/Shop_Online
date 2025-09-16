<div class="mt-4 mb-4">
    <x-card-public cardTitle="{{ __('checkout.checkout') }}">
        <div>
            <div class="card-body p-4">
                <form wire:submit.prevent="processPayment">
                    <!-- Dirección de Envío -->
                    <h5 class="mb-4 text-primary fw-bold">{{ __('checkout.shipping_address') }}</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="street" class="form-label">{{ __('checkout.street') }}</label>
                            <input type="text" id="street" wire:model.defer="street" class="form-control" placeholder="{{ __('checkout.street_placeholder') }}" />
                            @error('street') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="postalCode" class="form-label">{{ __('checkout.postal_code') }}</label>
                            <input type="text" id="postalCode" wire:model.defer="postalCode" class="form-control" placeholder="{{ __('checkout.postal_code_placeholder') }}" />
                            @error('postalCode') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">{{ __('checkout.city') }}</label>
                            <input type="text" id="city" wire:model.defer="city" class="form-control" placeholder="{{ __('checkout.city_placeholder') }}" />
                            @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="province" class="form-label">{{ __('checkout.province') }}</label>
                            <input type="text" id="province" wire:model.defer="province" class="form-control" placeholder="{{ __('checkout.province_placeholder') }}" />
                            @error('province') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <h5 class="mb-4 text-primary fw-bold">{{ __('checkout.payment_method') }}</h5>
                    <div class="form-check form-check-inline mb-3">
                        <input class="form-check-input" type="radio" id="paypal" value="paypal" wire:model="paymentMethod">
                        <label class="form-check-label d-flex align-items-center" for="paypal">
                            <i class="bi bi-paypal me-2"></i> {{ __('checkout.paypal') }}
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="card" value="card" wire:model="paymentMethod">
                        <label class="form-check-label d-flex align-items-center" for="card">
                            <i class="bi bi-credit-card me-2"></i> {{ __('checkout.credit_card') }}
                        </label>
                    </div>
                    @error('paymentMethod') <span class="text-danger small d-block">{{ $message }}</span> @enderror

                    <!-- Datos de Tarjeta -->
                    @if ($paymentMethod === 'card')
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <label for="cardNumber" class="form-label">{{ __('checkout.card_number') }}</label>
                                <input type="text" id="cardNumber" wire:model.defer="cardNumber" class="form-control" placeholder="{{ __('checkout.card_number_placeholder') }}">
                                @error('cardNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expiryDate" class="form-label">{{ __('checkout.expiry_date') }}</label>
                                <input type="text" id="expiryDate" wire:model.defer="expiryDate" class="form-control" placeholder="{{ __('checkout.expiry_date_placeholder') }}">
                                @error('expiryDate') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">{{ __('checkout.cvv') }}</label>
                                <input type="text" id="cvv" wire:model.defer="cvv" class="form-control" placeholder="{{ __('checkout.cvv_placeholder') }}">
                                @error('cvv') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Botón de Finalizar -->
                    <button type="submit" class="btn btn-primary w-100 mt-4">{{ __('checkout.confirm_purchase') }}</button>
                </form>
            </div>
        </div>
    </x-card-public>
</div>
