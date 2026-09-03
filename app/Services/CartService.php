<?php

namespace App\Services;

use App\Enums\CartStatus;
use App\Enums\CartStockActions;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Obtiene el carrito pendiente del usuario actual o crea uno nuevo.
     *
     * @return Cart|null
     */
    public function getOrCreatePendingCart(): ?Cart
    {
        $user = Auth::user();
        if ($user) {
            // Buscar el carrito con estado "pending" o crear uno nuevo
            $cart = Cart::where('user_id', $user->id)->where('status', CartStatus::PENDING)->first();
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'status' => CartStatus::PENDING,
                ]);
            }
            return $cart;
        }
        return null;
    }

    /**
     * Agrega un producto al carrito del usuario.
     *
     * @param int $productId
     * @return void
     */
    public function addToCart(int $productId): void
    {
        $cart = $this->getOrCreatePendingCart();

        if (empty($cart)) {
            return;
        }

        DB::transaction(function () use ($productId, $cart) {

            $product = Product::lockForUpdate()->findOrFail($productId);

            $available = $product->stock - $product->reserved_stock;

            if ($available >= 1) {

                $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
                if (!empty($cartItem)) {
                    $cartItem->increment('quantity');
                    $cartItem->update(['reserved_until' => now()->addMinutes(30)]);
                } else {
                    $cart->cartItems()->create([
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'unit_price' => $product->price,
                        'reserved_until' => now()->addMinutes(30)
                    ]);
                }
                $product->increment('reserved_stock');
            }
        });
    }

    /**
     * Actualiza el stock de un producto tanto en Product como en Cart.
     *
     * @param integer $itemId
     * @param CartStockActions $mode
     */
    public function updateStockProduct(int $itemId, CartStockActions $mode): void
    {
        $cart = $this->getOrCreatePendingCart();

        if (empty($cart)) {
            return;
        }

        DB::transaction(function () use ($itemId, $mode, $cart) {
            $cartItem = $cart->cartItems()->where('id', $itemId)->lockForUpdate()->first();

            if (empty($cartItem)) {
                return;
            }

            $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

            $available = $product->stock - $product->reserved_stock;

            if (CartStockActions::INCREMENT === $mode && $available >= 1) {
                $cartItem->increment('quantity');
                $cartItem->update(['reserved_until' => now()->addMinutes(30)]);
                $product->increment('reserved_stock');
            } elseif (CartStockActions::DECREMENT === $mode) {
                if ($cartItem->quantity > 1) {
                    $cartItem->decrement('quantity');
                    $cartItem->update(['reserved_until' => now()->addMinutes(30)]);
                } else {
                    $cartItem->delete();
                }
                $product->decrement('reserved_stock');
            } elseif (CartStockActions::DELETE === $mode) {
                $reservedStock = $cartItem->quantity;
                $product->decrement('reserved_stock', $reservedStock);
                $cartItem->delete();
            }
        });
    }

    /**
     * Carga los datos del carrito actual.
     *
     * @return array
     */
    public function loadCart(): array
    {
        $cart = $this->getOrCreatePendingCart();
        if ($cart) {
            return [
                'items' => $cart->cartItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->product->name,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                    ];
                })->toArray(),
                'total' => $cart->cartItems->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                }),
            ];
        }
        return ['items' => [], 'total' => 0];
    }
}
