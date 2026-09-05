<?php

namespace App\Jobs;

use App\Models\Cart;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class GenerateInvoiceJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Cart $cart
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(InvoiceService $invoiceService): void
    {
        $invoiceService->generateInvoice($this->cart);
    }
}
