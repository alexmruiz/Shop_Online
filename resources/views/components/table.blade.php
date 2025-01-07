<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <!-- Controles para mostrar entradas -->
    <div class="d-flex align-items-center gap-2">
        <span class="fw-semibold">Mostrar</span>
        <select wire:model.live="cant" class="form-select form-select-sm w-auto">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="fw-semibold">Entradas</span>
    </div>

    <!-- Campo de búsqueda -->
    <div class="input-group w-auto">
        <input type="text" wire:model.live="search" class="form-control form-control-sm text-white" placeholder="Buscar...">
        <span class="input-group-text bg-primary text-white">
            <i class="bi bi-search text-white"></i>
        </span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered text-center align-middle">
        <thead class="table">
            <tr>
                {{ $thead }}
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
