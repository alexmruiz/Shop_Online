<x-modal modalId="modalProduct" modalTitle="Productos" modalSize="modal-lg">
    <form wire:submit.prevent="{{$Id==0 ? "store" : "update($Id)"}} ">
        <div class="form-row">
            {{--Input Name--}}
            <div class="form-group col-md-6">
                <label for="name">Nombre:</label>
                <input wire:model='name' type="text" class="form-control" id="name" placeholder="Nombre del Producto">
                @error('name')
                    <div class="alert alert-danger w-100 mt-2">{{$message}}</div>
                @enderror
            </div>
            {{--Select category--}}
            <div class="form-group col-md-6">
                <label for="category_id">Categoria:</label>
                <select wire:model='category_id' id="category_id" class="form-control">
                    <option value="0">Seleccionar </option>
                    @foreach ($this->categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach                   
                </select>
                @error('category_id')
                    <div class="alert alert-danger w-100 mt-2">{{$message}}</div>
                @enderror
            </div>  
            {{--Textarea Descripción--}}
            <div class="form-group col-md-12">
                <label for="category_id">Descripción:</label>

                <textarea wire:model='description' id="description" class="form-control" cols="30" rows="3">
                </textarea>
                

                @error('description')
                    <div class="alert alert-danger w-100 mt-2">{{$message}}</div>
                @enderror
            </div>
            {{--Input precio--}}
            <div class="form-group col-md-4">
                <label for="price">Precio:</label>
                <input wire:model='price' min="0" type="number" step="any" class="form-control" id="price" 
                placeholder="Precio">
                @error('price')
                    <div class="alert alert-danger w-100 mt-2">{{$message}}</div>
                @enderror
            </div>         
            {{--Checkbox is_active--}}
            <div class="form-group col-md-4">
                <div class="icheck-primary">
                    <input wire:model='is_active' type="checkbox" id="is_active" checked>
                    <label for="is_active">¿Está activo?</label>
                </div>
                @error('is_active')
                    <div class="alert alert-danger w-100 mt-2">{{$message}}</div>
                @enderror
            </div> 
        </div>
        <hr>
        <button wire:loading.attr='disabled' class="btn btn-primary float-right">{{$Id==0 ? 'Guardar' : 'Editar'}}</button>
    </form>
</x-modal>