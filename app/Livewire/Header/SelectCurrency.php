<?php

namespace App\Livewire\Header;

use App\Enums\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SelectCurrency extends Component
{
    public array $currencies = [];

    public string $currencyUser = '';

    public function mount(): void
    {
       $this->currencies = Currency::cases();
       $this->currencyUser = Auth::check() ? Auth::user()->currency->value : Currency::EUR->value;
    }

    public function updatedCurrencyUser(string $value): void
    {
        $this->validate(['currencyUser' => ['required', Rule::enum(Currency::class)]]);

        if ($user = Auth::user()) {
            $user->currency = Currency::from($value);
            $user->save();
        }

        $this->dispatch('currencyuser-updated', currency:$this->currencyUser);
    }

    public function render()
    {
        return view('livewire.header.select-currency');
    }
}
