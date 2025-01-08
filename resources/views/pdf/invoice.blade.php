<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Pedido {{ $cart->order_number ?? 'N/A' }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        .container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .header .logo img {
            width: 40px;
            height: 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 2em;
            color: #555;
        }
        .header p {
            margin: 5px 0;
            font-size: 1.2em;
            color: #777;
        }
        .order-details th {
            background-color: #555;
            color: white;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        .order-details th, .order-details td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-details tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total {
            margin-top: 20px;
            font-size: 1.4em;
            text-align: right;
            font-weight: bold;
            color: #555;
        }
        .shipping-address {
            margin-top: 20px;
        }
        .shipping-address h3 {
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">
            <h1><i class="bi bi-shop"></i> Tienda AmR</h1>
        </div>
        <p>Factura de Compra</p>
        <p>Pedido número: {{ $cart->order_number ?? 'N/A' }}</p>
        <p>Cliente: {{ $userName }}</p>
        <p>Fecha: {{ $cart->created_at ? $cart->created_at->format('d/m/Y') : 'N/A' }}</p>
    </div>
    

    <div class="order-details">
        <h3>Detalles del Pedido</h3>
        <table class="order-details">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($cart->cartItems as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity ?? 0 }}</td>
                        <td>{{ number_format($item->unit_price ?? 0, 2) }} €</td>
                        <td>{{ number_format(($item->unit_price ?? 0) * ($item->quantity ?? 0), 2) }} €</td>
                    </tr>
                    @php
                        $total += ($item->unit_price ?? 0) * ($item->quantity ?? 0);
                    @endphp
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        Total: {{ number_format($total, 2) }} €
    </div>

    <div class="shipping-address">
        <h3>Dirección de Envío</h3>
        <p>{{ $cart->address ?? 'N/A' }}</p>
        <p>
            Recuerde que dispone de <strong>30 días</strong> para realizar una devolución.
        </p>
    </div>
</div>

<div class="footer">
    <p>Gracias por comprar en Tienda AmR. ¡Esperamos verte de nuevo!</p>
</div>
</body>
</html>
