<?php

/**
 * Database Query Benchmark Example
 * 
 * This example demonstrates how caching dramatically reduces processing time
 * for expensive database queries with large datasets.
 */

require __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

// Configuration
define('DATASET_SIZE', 10000);
define('QUERY_ITERATIONS', 5);

echo "=================================================================\n";
echo "DATABASE QUERY BENCHMARK - Large Dataset Performance\n";
echo "=================================================================\n\n";

// Initialize cache
$cache = Cache::make([
    'driver' => 'file',
    'path' => sys_get_temp_dir() . '/cache-benchmark',
    'prefix' => 'db:',
    'ttl' => 3600
]);

// Simulate a large database result
function simulateDatabaseQuery(int $size): array
{
    $data = [];
    
    // Simulate expensive database operations
    for ($i = 1; $i <= $size; $i++) {
        // Simulate row processing with complex calculations
        $data[] = [
            'id' => $i,
            'user_id' => rand(1, 1000),
            'product_name' => 'Product ' . $i,
            'description' => str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 5),
            'price' => round(rand(1000, 100000) / 100, 2),
            'quantity' => rand(1, 100),
            'category' => ['Electronics', 'Clothing', 'Food', 'Books', 'Sports'][rand(0, 4)],
            'rating' => round(rand(10, 50) / 10, 1),
            'reviews_count' => rand(0, 500),
            'created_at' => date('Y-m-d H:i:s', time() - rand(0, 31536000)),
            'metadata' => [
                'tags' => array_map(fn($n) => "tag_$n", range(1, rand(3, 10))),
                'attributes' => [
                    'weight' => rand(100, 5000) . 'g',
                    'dimensions' => rand(10, 100) . 'x' . rand(10, 100) . 'x' . rand(10, 100) . 'cm',
                    'color' => ['Red', 'Blue', 'Green', 'Black', 'White'][rand(0, 4)]
                ]
            ]
        ];
        
        // Simulate database latency
        if ($i % 100 === 0) {
            usleep(1000); // 1ms delay per 100 rows
        }
    }
    
    return $data;
}

// Simulate complex data aggregation
function aggregateData(array $data): array
{
    $aggregated = [
        'total_products' => count($data),
        'total_revenue' => 0,
        'total_quantity' => 0,
        'categories' => [],
        'average_rating' => 0,
        'price_range' => ['min' => PHP_FLOAT_MAX, 'max' => 0],
        'top_rated' => [],
    ];
    
    $totalRating = 0;
    
    foreach ($data as $item) {
        $aggregated['total_revenue'] += $item['price'] * $item['quantity'];
        $aggregated['total_quantity'] += $item['quantity'];
        $totalRating += $item['rating'];
        
        if (!isset($aggregated['categories'][$item['category']])) {
            $aggregated['categories'][$item['category']] = 0;
        }
        $aggregated['categories'][$item['category']]++;
        
        $aggregated['price_range']['min'] = min($aggregated['price_range']['min'], $item['price']);
        $aggregated['price_range']['max'] = max($aggregated['price_range']['max'], $item['price']);
        
        if ($item['rating'] >= 4.5) {
            $aggregated['top_rated'][] = $item['id'];
        }
    }
    
    $aggregated['average_rating'] = round($totalRating / count($data), 2);
    $aggregated['total_revenue'] = round($aggregated['total_revenue'], 2);
    
    return $aggregated;
}

echo "Dataset Size: " . number_format(DATASET_SIZE) . " records\n";
echo "Query Iterations: " . QUERY_ITERATIONS . "\n\n";

// ========================================
// WITHOUT CACHE
// ========================================
echo "--- WITHOUT CACHE ---\n";
$timesWithoutCache = [];

for ($i = 1; $i <= QUERY_ITERATIONS; $i++) {
    $start = microtime(true);
    
    // Simulate database query
    $data = simulateDatabaseQuery(DATASET_SIZE);
    
    // Process and aggregate data
    $result = aggregateData($data);
    
    $end = microtime(true);
    $duration = ($end - $start) * 1000; // Convert to milliseconds
    $timesWithoutCache[] = $duration;
    
    echo "  Query $i: " . number_format($duration, 2) . " ms\n";
}

$avgWithoutCache = array_sum($timesWithoutCache) / count($timesWithoutCache);
echo "  Average: " . number_format($avgWithoutCache, 2) . " ms\n\n";

// ========================================
// WITH CACHE (First call - cache miss)
// ========================================
echo "--- WITH CACHE (First Call - Cache Miss) ---\n";
$cache->clear(); // Ensure clean state

$start = microtime(true);

$result = $cache->remember('large_dataset_query', 3600, function() {
    $data = simulateDatabaseQuery(DATASET_SIZE);
    return aggregateData($data);
});

$end = microtime(true);
$cacheMissDuration = ($end - $start) * 1000;

echo "  Query 1 (cache miss): " . number_format($cacheMissDuration, 2) . " ms\n\n";

// ========================================
// WITH CACHE (Subsequent calls - cache hit)
// ========================================
echo "--- WITH CACHE (Subsequent Calls - Cache Hit) ---\n";
$timesWithCache = [];

for ($i = 1; $i <= QUERY_ITERATIONS; $i++) {
    $start = microtime(true);
    
    $result = $cache->remember('large_dataset_query', 3600, function() {
        $data = simulateDatabaseQuery(DATASET_SIZE);
        return aggregateData($data);
    });
    
    $end = microtime(true);
    $duration = ($end - $start) * 1000;
    $timesWithCache[] = $duration;
    
    echo "  Query $i: " . number_format($duration, 2) . " ms\n";
}

$avgWithCache = array_sum($timesWithCache) / count($timesWithCache);
echo "  Average: " . number_format($avgWithCache, 2) . " ms\n\n";

// ========================================
// PERFORMANCE SUMMARY
// ========================================
echo "=================================================================\n";
echo "PERFORMANCE SUMMARY\n";
echo "=================================================================\n\n";

echo "Without Cache:\n";
echo "  Average Time: " . number_format($avgWithoutCache, 2) . " ms\n";
echo "  Total Time:   " . number_format(array_sum($timesWithoutCache), 2) . " ms\n\n";

echo "With Cache (hits only):\n";
echo "  Average Time: " . number_format($avgWithCache, 2) . " ms\n";
echo "  Total Time:   " . number_format(array_sum($timesWithCache), 2) . " ms\n\n";

$speedup = $avgWithoutCache / $avgWithCache;
$timeSaved = $avgWithoutCache - $avgWithCache;
$percentageFaster = (($avgWithoutCache - $avgWithCache) / $avgWithoutCache) * 100;

echo "Performance Improvement:\n";
echo "  Speed Increase: " . number_format($speedup, 2) . "x faster\n";
echo "  Time Saved:     " . number_format($timeSaved, 2) . " ms per query\n";
echo "  Efficiency:     " . number_format($percentageFaster, 2) . "% faster\n\n";

// Calculate memory usage
$dataSize = strlen(serialize($result));
echo "Cached Data Size: " . number_format($dataSize / 1024, 2) . " KB\n\n";

// Show sample result
echo "Sample Result Data:\n";
echo "  Total Products:  " . number_format($result['total_products']) . "\n";
echo "  Total Revenue:   $" . number_format($result['total_revenue'], 2) . "\n";
echo "  Total Quantity:  " . number_format($result['total_quantity']) . "\n";
echo "  Average Rating:  " . $result['average_rating'] . "/5.0\n";
echo "  Categories:      " . implode(', ', array_keys($result['categories'])) . "\n";
echo "  Top Rated Items: " . count($result['top_rated']) . " products\n\n";

echo "=================================================================\n";
echo "CONCLUSION: Caching provides " . number_format($speedup, 1) . "x performance boost!\n";
echo "=================================================================\n";

// Cleanup
$cache->clear();
