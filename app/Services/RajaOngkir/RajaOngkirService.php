<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('rajaongkir.api_key');
        $this->baseUrl = config('rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
    }

    public function getProvinces(): array
    {
        return Cache::remember('rajaongkir_provinces', 86400 * 7, function () {
            try {
                $response = Http::withHeaders(['key' => $this->apiKey])
                    ->get($this->baseUrl . '/province');

                if ($response->successful()) {
                    return $response->json('rajaongkir.results', []);
                }
            } catch (\Exception $e) {
                Log::error('RajaOngkir getProvinces failed', ['error' => $e->getMessage()]);
            }
            return [];
        });
    }

    public function getCities(?int $provinceId = null): array
    {
        $cacheKey = 'rajaongkir_cities_' . ($provinceId ?? 'all');

        return Cache::remember($cacheKey, 86400 * 7, function () use ($provinceId) {
            try {
                $params   = $provinceId ? ['province' => $provinceId] : [];
                $response = Http::withHeaders(['key' => $this->apiKey])
                    ->get($this->baseUrl . '/city', $params);

                if ($response->successful()) {
                    return $response->json('rajaongkir.results', []);
                }
            } catch (\Exception $e) {
                Log::error('RajaOngkir getCities failed', ['error' => $e->getMessage()]);
            }
            return [];
        });
    }

    public function calculateShipping(int $originCityId, int $destinationCityId, int $weightGrams, string $courier): array
    {
        $weight   = max(1000, $weightGrams); // min 1kg
        $cacheKey = "shipping_{$originCityId}_{$destinationCityId}_{$weight}_{$courier}";

        return Cache::remember($cacheKey, 3600, function () use ($originCityId, $destinationCityId, $weight, $courier) {
            try {
                $response = Http::withHeaders(['key' => $this->apiKey])
                    ->post($this->baseUrl . '/cost', [
                        'origin'      => $originCityId,
                        'destination' => $destinationCityId,
                        'weight'      => $weight,
                        'courier'     => strtolower($courier),
                    ]);

                if ($response->successful()) {
                    $results = $response->json('rajaongkir.results', []);
                    return $results[0]['costs'] ?? [];
                }
            } catch (\Exception $e) {
                Log::error('RajaOngkir calculateShipping failed', ['error' => $e->getMessage()]);
            }
            return [];
        });
    }
}
