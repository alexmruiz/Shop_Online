<div class="input-group input-group-sm w-auto">
    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
        <i class="bi bi-currency-exchange text-primary" aria-hidden="true"></i>
    </span>

    <label class="visually-hidden" for="currencyMenu">Seleccionar moneda</label>
    <select
        class="form-select border-start-0 rounded-end-pill"
        id="currencyMenu"
        name="currencyMenu"
        wire:model.live="currencyUser"
    >
        @foreach ($currencies as $currency)
            <option value="{{ $currency->value }}">
                {{ strtoupper($currency->value) }}
            </option>
        @endforeach
    </select>
</div>