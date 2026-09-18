<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Evita llamadas HTTP externas durante los tests.
        Http::fake([
            '*' => Http::response([], 200),
        ]);
    }
    //
}
