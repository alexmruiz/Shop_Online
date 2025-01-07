<div class="mt-4 mb-4">
    <x-card-public cardTitle="Finalizar compra">
        <div>
            <div class="card-body p-4">
                <form wire:submit.prevent="processPayment">
                    <!-- Dirección de Envío -->
                    <h5 class="mb-4 text-primary fw-bold">Dirección de Envío</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="street" class="form-label">Dirección</label>
                            <input type="text" id="street" wire:model.defer="street" class="form-control" placeholder="Calle, número, piso" />
                            @error('street') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="postalCode" class="form-label">Código Postal</label>
                            <input type="text" id="postalCode" wire:model.defer="postalCode" class="form-control" placeholder="Ej: 28001"  />
                            @error('postalCode') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">Ciudad</label>
                            <input type="text" id="city" wire:model.defer="city" class="form-control" placeholder="Ej: Madrid" />
                            @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="province" class="form-label">Provincia</label>
                            <input type="text" id="province" wire:model.defer="province" class="form-control" placeholder="Ej: Madrid" />
                            @error('province') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <h5 class="mb-4 text-primary fw-bold">Método de Pago</h5>
                    <div class="form-check form-check-inline mb-3">
                        <input class="form-check-input" type="radio" id="paypal" value="paypal" wire:model="paymentMethod" >
                        <label class="form-check-label" for="paypal">PayPal</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="card" value="card" wire:model="paymentMethod" >
                        <label class="form-check-label" for="card">Tarjeta de Crédito</label>
                    </div>
                    @error('paymentMethod') <span class="text-danger small d-block">{{ $message }}</span> @enderror

                    <!-- Datos de Tarjeta -->
                    @if ($paymentMethod === 'card')
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <label for="cardNumber" class="form-label">Número de Tarjeta</label>
                                <input type="text" id="cardNumber" wire:model.defer="cardNumber" class="form-control" placeholder="16 dígitos">
                                @error('cardNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expiryDate" class="form-label">Fecha de Expiración</label>
                                <input type="text" id="expiryDate" wire:model.defer="expiryDate" class="form-control" placeholder="MM/YY">
                                @error('expiryDate') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" id="cvv" wire:model.defer="cvv" class="form-control" placeholder="3 dígitos">
                                @error('cvv') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Botón de Finalizar -->
                    <button type="submit" class="btn btn-primary w-100 mt-4">Confirmar Compra</button>
                </form>
            </div>
        </div>
    </x-card-public>
</div>
