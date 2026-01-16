<?php

/**
 * API Response Caching Benchmark Example
 * 
 * This example demonstrates how caching reduces API call latency
 * and prevents rate limiting issues with external services.
 */

require __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

// Configuration
define('API_CALLS', 10);
define('SIMULATED_API_LATENCY', 500); // milliseconds

echo "=================================================================\n";
echo "API RESPONSE CACHING BENCHMARK\n";
echo "=================================================================\n\n";

// Initialize cache
$cache = Cache::make([
    'driver' => 'file',
    'path' => sys_get_temp_dir() . '/cache-benchmark',
    'prefix' => 'api:',
    'ttl' => 1800
]);

// Simulate external API call with network latency
function simulateApiCall(string $endpoint): array
{
    // Simulate network latency
    usleep(SIMULATED_API_LATENCY * 1000);
    
    // Simulate different API responses
    $responses = [
        'users' => generateUserData(),
        'products' => generateProductData(),
        'analytics' => generateAnalyticsData(),
        'weather' => generateWeatherData(),
    ];
    
    return $responses[$endpoint] ?? ['error' => 'Unknown endpoint'];
}

function generateUserData(): array
{
    $users = [];
    for ($i = 1; $i <= 1000; $i++) {
        $users[] = [
            'id' => $i,
            'name' => 'User ' . $i,
            'email' => 'user' . $i . '@example.com',
            'role' => ['admin', 'user', 'moderator'][rand(0, 2)],
            'status' => ['active', 'inactive'][rand(0, 1)],
            'created_at' => date('Y-m-d', time() - rand(0, 31536000)),
            'profile' => [
                'avatar' => 'https://example.com/avatar/' . $i . '.jpg',
                'bio' => str_repeat('Lorem ipsum dolor sit amet. ', rand(5, 15)),
                'location' => ['New York', 'London', 'Tokyo', 'Paris', 'Sydney'][rand(0, 4)],
                'followers' => rand(0, 10000),
                'following' => rand(0, 1000),
            ]
        ];
    }
    return ['users' => $users, 'total' => count($users), 'timestamp' => time()];
}

function generateProductData(): array
{
    $products = [];
    for ($i = 1; $i <= 500; $i++) {
        $products[] = [
            'id' => $i,
            'sku' => 'PROD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            'name' => 'Product ' . $i,
            'description' => str_repeat('High quality product description. ', rand(3, 8)),
            'price' => round(rand(1000, 50000) / 100, 2),
            'stock' => rand(0, 500),
            'category' => ['Electronics', 'Clothing', 'Home', 'Sports', 'Books'][rand(0, 4)],
            'images' => array_map(fn($n) => "https://example.com/img/$i-$n.jpg", range(1, rand(3, 6))),
            'specifications' => [
                'weight' => rand(100, 5000) . 'g',
                'dimensions' => rand(10, 100) . 'x' . rand(10, 100) . 'x' . rand(10, 100),
                'material' => ['Plastic', 'Metal', 'Wood', 'Fabric'][rand(0, 3)],
            ]
        ];
    }
    return ['products' => $products, 'total' => count($products), 'timestamp' => time()];
}

function generateAnalyticsData(): array
{
    $data = [
        'pageviews' => rand(10000, 100000),
        'unique_visitors' => rand(5000, 50000),
        'bounce_rate' => round(rand(20, 80), 2),
        'avg_session_duration' => rand(60, 600),
        'conversion_rate' => round(rand(100, 500) / 100, 2),
        'revenue' => round(rand(10000, 1000000) / 100, 2),
        'top_pages' => array_map(fn($i) => [
            'url' => '/page-' . $i,
            'views' => rand(100, 10000),
            'avg_time' => rand(30, 300)
        ], range(1, 20)),
        'traffic_sources' => [
            'organic' => rand(30, 50),
            'direct' => rand(20, 40),
            'social' => rand(10, 30),
            'referral' => rand(5, 20),
            'email' => rand(5, 15),
        ],
        'timestamp' => time()
    ];
    return $data;
}

function generateWeatherData(): array
{
    return [
        'location' => 'San Francisco, CA',
        'current' => [
            'temperature' => rand(10, 30),
            'feels_like' => rand(8, 32),
            'humidity' => rand(40, 90),
            'wind_speed' => rand(0, 30),
            'condition' => ['Sunny', 'Cloudy', 'Rainy', 'Partly Cloudy'][rand(0, 3)],
        ],
        'forecast' => array_map(fn($day) => [
            'day' => date('l', strtotime("+$day days")),
            'high' => rand(15, 35),
            'low' => rand(5, 20),
            'precipitation' => rand(0, 100),
        ], range(1, 7)),
        'timestamp' => time()
    ];
}

$endpoints = ['users', 'products', 'analytics', 'weather'];

echo "Simulated API Latency: " . SIMULATED_API_LATENCY . " ms per call\n";
echo "Number of API Calls: " . API_CALLS . " per endpoint\n";
echo "Endpoints: " . implode(', ', $endpoints) . "\n\n";

// ========================================
// WITHOUT CACHE
// ========================================
echo "--- WITHOUT CACHE (Direct API Calls) ---\n";
$totalTimeWithoutCache = 0;
$callsWithoutCache = [];

foreach ($endpoints as $endpoint) {
    echo "\nEndpoint: /$endpoint\n";
    $endpointTimes = [];
    
    for ($i = 1; $i <= API_CALLS; $i++) {
        $start = microtime(true);
        $response = simulateApiCall($endpoint);
        $end = microtime(true);
        
        $duration = ($end - $start) * 1000;
        $endpointTimes[] = $duration;
        $totalTimeWithoutCache += $duration;
        
        echo "  Call $i: " . number_format($duration, 2) . " ms\n";
    }
    
    $avgTime = array_sum($endpointTimes) / count($endpointTimes);
    $callsWithoutCache[$endpoint] = [
        'times' => $endpointTimes,
        'average' => $avgTime,
        'total' => array_sum($endpointTimes)
    ];
    
    echo "  Average: " . number_format($avgTime, 2) . " ms\n";
}

echo "\nTotal Time (all endpoints): " . number_format($totalTimeWithoutCache, 2) . " ms\n";
echo "Total API Calls Made: " . (count($endpoints) * API_CALLS) . "\n\n";

// ========================================
// WITH CACHE
// ========================================
echo "=================================================================\n";
echo "--- WITH CACHE ---\n";
$cache->clear(); // Ensure clean state

$totalTimeWithCache = 0;
$callsWithCache = [];
$cacheMisses = 0;
$cacheHits = 0;

foreach ($endpoints as $endpoint) {
    echo "\nEndpoint: /$endpoint\n";
    $endpointTimes = [];
    
    for ($i = 1; $i <= API_CALLS; $i++) {
        $cacheKey = "api_response_{$endpoint}";
        
        $start = microtime(true);
        
        // Check if cached
        $isCached = $cache->has($cacheKey);
        
        $response = $cache->remember($cacheKey, 1800, function() use ($endpoint) {
            return simulateApiCall($endpoint);
        });
        
        $end = microtime(true);
        
        $duration = ($end - $start) * 1000;
        $endpointTimes[] = $duration;
        $totalTimeWithCache += $duration;
        
        if (!$isCached && $i === 1) {
            echo "  Call $i: " . number_format($duration, 2) . " ms (cache miss)\n";
            $cacheMisses++;
        } else {
            echo "  Call $i: " . number_format($duration, 2) . " ms (cache hit)\n";
            $cacheHits++;
        }
    }
    
    $avgTime = array_sum($endpointTimes) / count($endpointTimes);
    $callsWithCache[$endpoint] = [
        'times' => $endpointTimes,
        'average' => $avgTime,
        'total' => array_sum($endpointTimes)
    ];
    
    echo "  Average: " . number_format($avgTime, 2) . " ms\n";
}

echo "\nTotal Time (all endpoints): " . number_format($totalTimeWithCache, 2) . " ms\n";
echo "Cache Hits: $cacheHits\n";
echo "Cache Misses: $cacheMisses\n\n";

// ========================================
// PERFORMANCE COMPARISON
// ========================================
echo "=================================================================\n";
echo "PERFORMANCE COMPARISON\n";
echo "=================================================================\n\n";

foreach ($endpoints as $endpoint) {
    $withoutCache = $callsWithoutCache[$endpoint]['average'];
    $withCache = $callsWithCache[$endpoint]['average'];
    $speedup = $withoutCache / $withCache;
    $timeSaved = $withoutCache - $withCache;
    
    echo "Endpoint: /$endpoint\n";
    echo "  Without Cache: " . number_format($withoutCache, 2) . " ms\n";
    echo "  With Cache:    " . number_format($withCache, 2) . " ms\n";
    echo "  Speed Increase: " . number_format($speedup, 2) . "x faster\n";
    echo "  Time Saved:    " . number_format($timeSaved, 2) . " ms per call\n\n";
}

// Overall statistics
$overallSpeedup = $totalTimeWithoutCache / $totalTimeWithCache;
$overallTimeSaved = $totalTimeWithoutCache - $totalTimeWithCache;
$percentageFaster = (($totalTimeWithoutCache - $totalTimeWithCache) / $totalTimeWithoutCache) * 100;

echo "=================================================================\n";
echo "OVERALL STATISTICS\n";
echo "=================================================================\n\n";

echo "Total Time Without Cache: " . number_format($totalTimeWithoutCache, 2) . " ms\n";
echo "Total Time With Cache:    " . number_format($totalTimeWithCache, 2) . " ms\n";
echo "Total Time Saved:         " . number_format($overallTimeSaved, 2) . " ms\n\n";

echo "Performance Improvement:\n";
echo "  Speed Increase: " . number_format($overallSpeedup, 2) . "x faster\n";
echo "  Efficiency:     " . number_format($percentageFaster, 2) . "% faster\n";
echo "  Cache Hit Rate: " . number_format(($cacheHits / ($cacheHits + $cacheMisses)) * 100, 2) . "%\n\n";

// Cost savings calculation
$apiCostPerCall = 0.001; // $0.001 per API call
$callsSaved = $cacheHits;
$costSavings = $callsSaved * $apiCostPerCall;

echo "Cost Savings (at $" . $apiCostPerCall . " per API call):\n";
echo "  API Calls Saved: $callsSaved\n";
echo "  Money Saved:     $" . number_format($costSavings, 4) . "\n";
echo "  Monthly Savings: $" . number_format($costSavings * 30 * 24 * 60, 2) . " (at 1 req/min)\n\n";

echo "=================================================================\n";
echo "CONCLUSION: Caching eliminates " . number_format($percentageFaster, 1) . "% of API latency!\n";
echo "=================================================================\n";

// Cleanup
$cache->clear();
