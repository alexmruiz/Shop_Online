<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class GetCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyService $currency)
    {
        $saved = $currency->getCurrencyApi();

        if (!$saved) {
            $this->error('No se pudieron actualizar los tipos de cambio.');

            return self::FAILURE;
        }

        $this->info("Actualización correcta.");

        return self::SUCCESS;
    }
}
