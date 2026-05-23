<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\RajaOngkir\RajaOngkirService::class);
echo "Testing getProvinces():\n";
$provinces = $service->getProvinces();
var_dump($provinces);

echo "\nTesting API call manually:\n";
$apiKey = config('rajaongkir.api_key');
$baseUrl = config('rajaongkir.base_url');
echo "API Key: $apiKey\n";
echo "Base URL: $baseUrl\n";

try {
    $response = Illuminate\Support\Facades\Http::withHeaders(['key' => $apiKey])
        ->get($baseUrl . '/province');
    echo "Status Code: " . $response->status() . "\n";
    echo "Body:\n" . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Error calling API: " . $e->getMessage() . "\n";
}
