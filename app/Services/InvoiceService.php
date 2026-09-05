<?php

namespace App\Services;

use App\Models\Cart;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class InvoiceService
{
    /**
     * Genera la última factura confirmada del usuario autenticado.
     *
     * @param Cart $cart
     * @return Response
     */
    public function generateInvoice(Cart $cart): Response
    {
        // Cargar relaciones si no están presentes
        $cart->loadMissing(['cartItems.product', 'user']);

        return $this->buildPdfResponse($cart, 'attachment');
    }

    /**
     * Descarga una factura por ID (inline en navegador).
     *
     * @param integer $id
     * @return Response
     */
    public function downloadInvoiceByCartId(int $id): Response
    {
        $cart = Cart::with('cartItems.product', 'user')->findOrFail($id);

        Gate::authorize('view', $cart);

        return $this->buildPdfResponse($cart, 'inline');
    }

    /* ===============================
       Métodos privados de soporte
       =============================== */

    /**
     * Sanitiza los datos del carrito para evitar problemas de codificación.
     * @param \App\Models\Cart $cart
     * @return void
     */
    private function sanitizeCart(Cart $cart): void
    {
        $cart->address = mb_convert_encoding($cart->address, 'UTF-8', 'UTF-8');

        foreach ($cart->cartItems as $item) {
            $item->product->name = mb_convert_encoding($item->product->name, 'UTF-8', 'UTF-8');
        }
    }

    /**
     * Renderiza el PDF de la factura.
     * @param \App\Models\Cart $cart
     * @return Dompdf
     */
    private function renderPdf(Cart $cart): Dompdf
    {
        $this->sanitizeCart($cart);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('pdf.invoice', [
            'cart' => $cart,
            'userName' => $cart->user->name ?? 'Cliente desconocido'
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Construye la respuesta HTTP con el PDF generado.
     * @param \App\Models\Cart $cart
     * @param string $disposition
     * @return Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    private function buildPdfResponse(Cart $cart, string $disposition = 'attachment'): Response
    {
        $dompdf = $this->renderPdf($cart);

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="factura_' . $cart->order_number . '.pdf"',
        ]);
    }
}
