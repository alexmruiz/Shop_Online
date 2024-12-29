@props(['product', 'class' => ''])

@if (empty($product->image))
    <img src="{{ asset('assets/images/no_imagen.png') }}" class="{{ $class }}" alt="Imagen no disponible"> 
@else
    <img src="{{ Storage::url($product->image) }}" class="{{ $class }}" alt="{{ $product->name }}">
@endif
