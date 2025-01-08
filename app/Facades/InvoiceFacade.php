<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class InvoiceFacade extends Facade
{
  
    protected static function getFacadeAccessor()
    {
        return "InvoiceService";
    }
}
