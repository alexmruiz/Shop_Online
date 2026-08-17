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
                            <input type="text" id="street" wire:model.live="street" class="form-control" placeholder="{{ __('checkout.street_placeholder') }}" />
                            @error('street') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="postalCode" class="form-label">{{ __('checkout.postal_code') }}</label>
                            <input type="text" id="postalCode" wire:model.live="postalCode" class="form-control" placeholder="{{ __('checkout.postal_code_placeholder') }}" />
                            @error('postalCode') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">{{ __('checkout.city') }}</label>
                            <input type="text" id="city" wire:model.live="city" class="form-control" placeholder="{{ __('checkout.city_placeholder') }}" />
                            @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="province" class="form-label">{{ __('checkout.province') }}</label>
                            <input type="text" id="province" wire:model.live="province" class="form-control" placeholder="{{ __('checkout.province_placeholder') }}" />
                            @error('province') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Mensaje / Indicador de estado de dirección -->
                    <div class="mb-3">
                        @if(empty(array_filter($originalAddress)))
                            <p class="text-info small mb-0">Se guardará esta dirección como predeterminada.</p>
                        @elseif($this->hasChanges)
                            <p class="text-warning small mb-0">Se han detectado cambios. Se actualizará la dirección al confirmar.</p>
                        @else
                            <p class="text-muted small mb-0">Dirección guardada. Realiza cambios si deseas actualizarla.</p>
                        @endif
                    </div>

                    <!-- Botón de Finalizar Compra -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ __('checkout.confirm_purchase') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-card-public>
</div>