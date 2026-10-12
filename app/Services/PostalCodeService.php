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

        try {
            $response = Http::connectTimeout(2)->timeout(4)->get($apiUrl);
        } catch (\Throwable $th) {
            report($th);
            return [
                'available' => false,
                'cities' => [],
                'province' => '',
            ];
        }

        if (!$response->successful()) {
            return [
                'available' => true,
                'cities' => [],
                'province' => '',
                'found' => false
            ];
        }

        $data = $response->json();

        $cities = [];
        $province = '';
        $available = false;

        if (!empty($data['places'])) {
            foreach ($data['places'] as $places) {
                $cities[$places['place name']] = $places['place name'] ?? '';
            }

            $province = $data['places'][0]['state'];
            $available = true;
            
        }

        return [
            'cities' => $cities,
            'province' => $province,
            'available' => $available
        ];
    }
}
