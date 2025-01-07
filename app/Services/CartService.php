<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Obtiene el carrito pendiente del usuario actual o crea uno nuevo.
     *
     * @return Cart|null
     */
    public function getOrCreatePendingCart()
    {
        $user = Auth::user();
        if ($user) {
            // Buscar el carrito con estado "pending" o crear uno nuevo
            $cart = $user->carts()->where('status', 'pending')->first();
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
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
    public function addToCart($productId)
    {
        $product = Product::find($productId);
        $cart = $this->getOrCreatePendingCart();

        if ($cart) {
            $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
            if ($cartItem) {
                $cartItem->increment('quantity');
            } else {
                $cart->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                ]);
            }
        }
    }

    /**
     * Incrementa la cantidad de un producto en el carrito.
     *
     * @param int $itemId
     * @return void
     */
    public function increaseQuantity($itemId)
    {
        $cart = $this->getOrCreatePendingCart();
        if ($cart) {
            $item = $cart->cartItems()->find($itemId);
            if ($item) {
                $item->increment('quantity');
            }
        }
    }

    /**
     * Decrementa la cantidad de un producto en el carrito.
     *
     * @param int $itemId
     * @return void
     */
    public function decreaseQuantity($itemId)
    {
        $cart = $this->getOrCreatePendingCart();
        if ($cart) {
            $item = $cart->cartItems()->find($itemId);
            if ($item) {
                if ($item->quantity > 1) {
                    $item->decrement('quantity');
                } else {
                    $this->removeFromCart($itemId);
                }
            }
        }
    }

    /**
     * Elimina un producto del carrito.
     *
     * @param int $itemId
     * @return void
     */
    public function removeFromCart($itemId)
    {
        $cart = $this->getOrCreatePendingCart();
        if ($cart) {
            $item = $cart->cartItems()->find($itemId);
            if ($item) {
                $item->delete();
            }
        }
    }

    /**
     * Carga los datos del carrito actual.
     *
     * @return array
     */
    public function loadCart()
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