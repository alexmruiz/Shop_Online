<x-modal modalId="modalClient" modalTitle="Clientes" modalSize="modal-lg">
    <form wire:submit.prevent="{{$Id==0 ? "store" : "update($Id)"}} ">
        <div class="form-row">
            {{-- Input Name --}}
            <div class="form-group col-md-6">
                <label for="name">Nombre:</label>
                <input wire:model="name" type="text" class="form-control" id="name" placeholder="Nombre del Producto">
                @error('name')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Email --}}
            <div class="form-group col-md-6">
                <label for="email">Correo Electrónico:</label>
                <input wire:model="email" type="email" class="form-control" id="email" placeholder="Correo Electrónico">
                @error('email')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Phone --}}
            <div class="form-group col-md-6">
                <label for="phone">Teléfono:</label>
                <input wire:model="phone" type="text" class="form-control" id="phone" placeholder="Teléfono">
                @error('phone')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Address --}}
            <div class="form-group col-md-6">
                <label for="address">Dirección:</label>
                <input wire:model="address" type="text" class="form-control" id="address" placeholder="Dirección">
                @error('address')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <hr>
        <button wire:loading.attr='disabled' class="btn btn-primary float-right">{{$Id==0 ? 'Guardar' : 'Editar'}}</button>
    </form>
</x-modal>