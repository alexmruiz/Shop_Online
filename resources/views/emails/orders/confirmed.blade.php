@component('mail::message')
# ¡Gracias por tu compra, {{ $cart->user->name }}!

Tu pedido **#{{ $cart->order_number }}** ha sido confirmado.

@component('mail::table')
| Producto | Cantidad | Precio |
|----------|----------|--------|
@foreach($cart->cartItems as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | {{ number_format($item->unit_price * $item->quantity, 2) }} € |
@endforeach
@endcomponent

**Total:** {{ number_format($cart->total, 2) }} €

@component('mail::button', ['url' => route('download.invoice', $cart->id)])
Descargar factura
@endcomponent

Gracias por confiar en nosotros.  
{{ config('app.name') }}
@endcomponent
