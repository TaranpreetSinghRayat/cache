<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

echo "=== API Response Caching Example ===\n\n";

$cache = Cache::make([
    'driver' => 'file',
    'path' => __DIR__ . '/cache',
    'prefix' => 'api:',
    'ttl' => 1800
]);

class WeatherAPI {
    public static function fetch(string $city): array {
        echo "[API] Calling weather API for {$city}...\n";
        sleep(2);
        
        return [
            'city' => $city,
            'temperature' => rand(60, 90),
            'condition' => ['sunny', 'cloudy', 'rainy'][rand(0, 2)],
            'humidity' => rand(30, 80),
            'timestamp' => time()
        ];
    }
}

class CryptoAPI {
    public static function getPrices(): array {
        echo "[API] Fetching cryptocurrency prices...\n";
        sleep(2);
        
        return [
            'BTC' => rand(40000, 50000),
            'ETH' => rand(2000, 3000),
            'ADA' => rand(1, 2)
        ];
    }
}

echo "1. Weather API Caching:\n";
echo "------------------------\n";
$weather = $cache->remember('weather:london', 1800, function() {
    return WeatherAPI::fetch('London');
});
echo "Weather data: " . json_encode($weather, JSON_PRETTY_PRINT) . "\n\n";

echo "Second request (from cache):\n";
$weatherCached = $cache->remember('weather:london', 1800, function() {
    return WeatherAPI::fetch('London');
});
echo "Weather data (cached): " . json_encode($weatherCached, JSON_PRETTY_PRINT) . "\n\n";

echo "2. Cryptocurrency Prices Caching:\n";
echo "----------------------------------\n";
$prices = $cache->remember('crypto:prices', 300, function() {
    return CryptoAPI::getPrices();
});
echo "Crypto prices: " . json_encode($prices, JSON_PRETTY_PRINT) . "\n\n";

echo "3. Multiple City Weather (Batch Caching):\n";
echo "------------------------------------------\n";
$cities = ['New York', 'Tokyo', 'Paris'];

foreach ($cities as $city) {
    $key = 'weather:' . strtolower(str_replace(' ', '_', $city));
    $data = $cache->remember($key, 1800, function() use ($city) {
        return WeatherAPI::fetch($city);
    });
    echo "{$city}: {$data['temperature']}°F, {$data['condition']}\n";
}

echo "\n4. API Rate Limiting with Cache:\n";
echo "---------------------------------\n";
function checkApiRateLimit(string $endpoint, $cache): bool {
    $key = "rate_limit:{$endpoint}";
    $requests = (int) $cache->get($key, 0);
    
    if ($requests >= 10) {
        echo "Rate limit exceeded for {$endpoint}\n";
        return false;
    }
    
    $cache->increment($key);
    
    if ($requests === 0) {
        $cache->set($key, 1, 3600);
    }
    
    echo "Request {$requests}/10 for {$endpoint}\n";
    return true;
}

for ($i = 0; $i < 12; $i++) {
    if (checkApiRateLimit('weather_api', $cache)) {
        echo "  -> API call allowed\n";
    } else {
        echo "  -> API call blocked\n";
    }
}

echo "\n5. Conditional Cache Refresh:\n";
echo "------------------------------\n";
function getDataWithRefresh(string $key, bool $forceRefresh, $cache): array {
    if ($forceRefresh) {
        echo "Force refresh requested, clearing cache...\n";
        $cache->delete($key);
    }
    
    return $cache->remember($key, 600, function() {
        echo "Fetching fresh data...\n";
        return ['data' => 'Fresh content', 'timestamp' => time()];
    });
}

$data1 = getDataWithRefresh('api:data', false, $cache);
echo "Data: " . json_encode($data1) . "\n\n";

$data2 = getDataWithRefresh('api:data', true, $cache);
echo "Data (refreshed): " . json_encode($data2) . "\n\n";

echo "=== Example Complete ===\n";
