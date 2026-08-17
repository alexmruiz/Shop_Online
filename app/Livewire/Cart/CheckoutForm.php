<?php

namespace App\Livewire\Cart;

use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
#[Layout('components.layouts.app_public')]
class CheckoutForm extends Component
{
    public string $street = '';
    public string $city = '';
    public string $postalCode = '';
    public string $province = '';
    public bool $saveAsDefault = true;

    public array $originalAddress = [];

    protected function rules(): array
    {
        return [
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postalCode' => ['required', 'digits:5'],
            'province' => ['required', 'string', 'max:255'],
        ];
    }

    public function mount()
    {
        $userAddress = Auth::user()->address ?? [];

        if (!empty($userAddress)) {
            $this->street = $userAddress['street'] ?? '';
            $this->city = $userAddress['city'] ?? '';
            $this->postalCode = (string)($userAddress['postalCode'] ?? '');
            $this->province = $userAddress['province'] ?? '';
        }

        // Guarda una copia asociativa idéntica de la dirección inicial
        $this->originalAddress = $this->getCurrentAddressData();
    }

    /**
     * Construye el array con el formato estándar de la dirección.
     *
     * @return array
     */
    private function getCurrentAddressData(): array
    {
        return [
            'street' => trim($this->street),
            'city' => trim($this->city),
            'postalCode' => trim($this->postalCode),
            'province' => trim($this->province),
        ];
    }

    /**
     * Evaluador reactivo: Verifica si hay cambios respecto al estado inicial.
     *
     * @return boolean
     */
    public function getHasChangesProperty(): bool
    {
        return $this->getCurrentAddressData() !== $this->originalAddress;
    }

    /**
     * Comprueba si la dirección se ha actualizado y la envía al servicio para iniciar el checkout
     *
     * @param CheckoutService $checkoutService
     * @return void
     */
    public function processPayment(CheckoutService $checkoutService)
    {
        $this->validate();

        $newAddress = $this->getCurrentAddressData();

        try {
            // Se debe guardar si no existía dirección o si el usuario realizó cambios
            $shouldSaveAddress = empty(array_filter($this->originalAddress)) || $this->hasChanges;

            return $checkoutService->process(
                Auth::user(),
                $newAddress,
                $this->saveAsDefault && $shouldSaveAddress
            );
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.cart.checkout-form');
    }
}
