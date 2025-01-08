<?php

namespace App\Services;

use App\Models\Cart;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;

class InvoiceService
{
    // Método para generar la factura para el pedido individual
    public function generateInvoice()
    {

        $cart = Auth::user()->carts()->where('status', 'confirmed')->latest()->first();

        if (!$cart) {
            return redirect()->route('home')->with('error', 'No se encontró un pedido confirmado.');
        }

        // Sanitizar datos
        $cart->address = mb_convert_encoding($cart->address, 'UTF-8', 'UTF-8');
        foreach ($cart->cartItems as $item) {
            $item->product->name = mb_convert_encoding($item->product->name, 'UTF-8', 'UTF-8');
        }

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Renderizar la vista
        $html = view('pdf.invoice', [
            'cart' => $cart,
            'userName' => $cart->user->name ?? 'Cliente desconocido'
        ])->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF con las cabeceras correctas
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="factura-' . $cart->order_number . '.pdf"',
        ]);
        
    }

    //Descarga el pdf asociado a una factura en concreto
    public function downloadInvoice($id)
    {
        // Obtener el carrito específico con los productos
        $cart = Cart::with('cartItems.product')->findOrFail($id);

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // Generar el HTML para el PDF (usando una vista Blade)
        $html = view('pdf.invoice', [
            'cart' => $cart,
            'userName' => $cart->user->name ?? 'Cliente desconocido'
        ])->render();
        
        // Cargar el HTML y generar el PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF generado
        return response()->stream(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="factura_' . $cart->order_number . '.pdf"'
            ]
        );
    }

    
}
