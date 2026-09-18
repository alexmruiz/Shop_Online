<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function saveStreet(User $user, array $addressData)
    {
        $user->update(['address' => [
                    'street' => $addressData['street'],
                    'city' => $addressData['city'],
                    'province' => $addressData['province'],
                    'postalCode' => $addressData['postalCode']
                ]]);
    }

    /**
     * Retorna la dirección formateada.
     * @param array $data
     * @return string
     */
    public function formatAddress(array $data): string
    {
        return "{$data['street']}, {$data['city']}, {$data['province']}, {$data['postalCode']}";
    }
}
