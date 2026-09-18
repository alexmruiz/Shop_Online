<?php

namespace App\Services;

use App\Enums\CartStatus;
use App\Exceptions\CheckoutService\StockReservationException;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderConfirmationService
{
    public function confirm(Cart $cart)
    {
        DB::transaction(function () use ($cart) {
            $cart->update([
                'status' => CartStatus::CONFIRMED,
                'order_number' => $this->generateOrderNumber(),
            ]);

            Log::info('Carrito confirmado', [
                'cart_id' => $cart->id,
                'order_number' => $cart->order_number,
            ]);

            $cartItems = $cart->cartItems;

            foreach ($cartItems as $ct) {
                $productId = $ct->product_id;
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->stock < $ct->quantity) {
                    throw new StockReservationException('La reserva de stock no es válida.');
                }

                // Decrementar stock
                $product->decrement('stock', $ct->quantity);
                $product->decrement('reserved_stock', $ct->quantity);

                $ct->update(['reserved_until' => null]);
            }
            GenerateInvoiceJob::dispatch($cart)->afterCommit();
            SendOrderConfirmationJob::dispatch($cart)->afterCommit();
        });
    }

    /**
     * Genera un número de orden único.
     * @return string
     */
    private function generateOrderNumber(): string
    {
        $date = now()->format('YmdHis');
        return $date . '-' . random_int(1000, 9999);
    }
}
