<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;
use App\Listeners\StripeWebhookListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el listener de Stripe/Cashier para webhooks
        Event::listen(WebhookReceived::class, [StripeWebhookListener::class, 'handle']);
    }
}
