<?php

namespace App\Console\Commands;

use App\Enums\CartStatus;
use App\Models\CartItem;
use App\Models\Product;
use App\Notifications\CartItemExpiredNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredCartReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release-expired-cart-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera las reservas de stock caducadas en los carritos pendientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $released_units = 0;
        CartItem::whereNotNull('reserved_until')
            ->where('reserved_until', '<=', now())
            ->whereHas('cart', fn($q) => $q->where('status', CartStatus::PENDING))
            ->with(['cart.user', 'product'])
            ->chunkById(100, function ($items) use (&$released_units) {
                foreach ($items as $item) {
                    DB::transaction(function () use ($item) {
                        $item->cart->user?->notify(
                            new CartItemExpiredNotification($item)
                        );
                        $product = Product::lockForUpdate()->findOrFail($item->product_id);

                        $product->decrement('reserved_stock', $item->quantity);
                        $item->delete();
                    });
                }
                $released_units += $item->quantity;
            });
        Log::info('Reservas expiradas liberadas', [
            'released_items' => $released_units,
        ]);
        $this->info('Reservas caducadas liberadas correctamente.');
    }
}
