<x-modal modalId="modalClient" modalTitle="Clientes" modalSize="modal-lg">
    <form wire:submit.prevent="{{ $Id == 0 ? 'store' : 'update' }}">
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

            {{-- Input Password --}}
            <div class="form-group col-md-6">
                <label for="password">Contraseña:</label>
                <input wire:model="password" type="password" class="form-control" id="password" placeholder="Contraseña">
                @error('password')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Confirmpassword --}}
            <div class="form-group col-md-6">
                <label for="confirmpassword">Confirmar Contraseña:</label>
                <input wire:model="confirmpassword" type="password" class="form-control" id="confirmpassword" placeholder="Confirmar Contraseña">
                @error('confirmpassword')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input Role --}}
            <div class="form-group col-md-6">
                <label for="role">Rol:</label>
                <select wire:model="role" class="form-control" id="role">
                    <option value="">Seleccionar Rol</option>
                    @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
                </select>
                @error('role')
                    <div class="alert alert-danger w-100 mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <hr>
        <button wire:loading.attr="disabled" class="btn btn-primary float-right">
            {{ $Id == 0 ? 'Guardar' : 'Editar' }}
        </button>
    </form>
</x-modal>
