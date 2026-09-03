<?php

namespace App\Console\Commands;

use App\Enums\CartStatus;
use App\Models\CartItem;
use App\Models\Product;
use App\Notifications\CartItemExpiredNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        CartItem::whereNotNull('reserved_until')
            ->where('reserved_until', '<=', now())
            ->whereHas('cart', fn($q) => $q->where('status', CartStatus::PENDING))
            ->with(['cart.user', 'product'])
            ->chunkById(100, function ($items) {
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
            });
        $this->info('Reservas caducadas liberadas correctamente.');
    }
}
