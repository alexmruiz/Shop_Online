<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PostalCodeService
{

    /**
     * Obtiene ciudad y provincia a partir del código postal.
     *
     * @param string $postalCode
     * @return array
     */
    public function updatedPostalCode(string $postalCode): array
    {
        $apiUrl = 'https://api.zippopotam.us/es/' . $postalCode;

        $response = Http::get($apiUrl);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();

        $city = [];

        if (!empty($data['places'])) {
            foreach ($data['places'] as $places) {
                $city[$places['place name']] = $places['place name'] ?? '';
            }
            return [
                'city' => $city,
                'province' => $data['places'][0]['state']
            ];
        }

        return $city;
    }
}
