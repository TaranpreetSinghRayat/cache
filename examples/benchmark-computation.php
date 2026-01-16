<?php

/**
 * Heavy Computation Caching Benchmark Example
 * 
 * This example demonstrates how caching expensive computational operations
 * can dramatically improve application performance.
 */

require __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

// Configuration
define('COMPUTATION_ITERATIONS', 3);

echo "=================================================================\n";
echo "HEAVY COMPUTATION BENCHMARK\n";
echo "=================================================================\n\n";

// Initialize cache
$cache = Cache::make([
    'driver' => 'file',
    'path' => sys_get_temp_dir() . '/cache-benchmark',
    'prefix' => 'compute:',
    'ttl' => 3600
]);

// ========================================
// COMPUTATION 1: Fibonacci Sequence (Recursive)
// ========================================
function fibonacciRecursive(int $n): int
{
    if ($n <= 1) return $n;
    return fibonacciRecursive($n - 1) + fibonacciRecursive($n - 2);
}

function calculateFibonacciSequence(int $count): array
{
    $sequence = [];
    for ($i = 0; $i <= $count; $i++) {
        $sequence[$i] = fibonacciRecursive($i);
    }
    return $sequence;
}

// ========================================
// COMPUTATION 2: Prime Number Generation
// ========================================
function isPrime(int $num): bool
{
    if ($num < 2) return false;
    if ($num === 2) return true;
    if ($num % 2 === 0) return false;
    
    $sqrt = (int)sqrt($num);
    for ($i = 3; $i <= $sqrt; $i += 2) {
        if ($num % $i === 0) return false;
    }
    return true;
}

function generatePrimes(int $limit): array
{
    $primes = [];
    for ($i = 2; $i <= $limit; $i++) {
        if (isPrime($i)) {
            $primes[] = $i;
        }
    }
    return $primes;
}

// ========================================
// COMPUTATION 3: Complex Statistical Analysis
// ========================================
function generateDataset(int $size): array
{
    $data = [];
    for ($i = 0; $i < $size; $i++) {
        $data[] = rand(1, 1000) / 10;
    }
    return $data;
}

function performStatisticalAnalysis(array $data): array
{
    sort($data);
    $count = count($data);
    
    // Mean
    $mean = array_sum($data) / $count;
    
    // Median
    $middle = floor($count / 2);
    $median = $count % 2 === 0 
        ? ($data[$middle - 1] + $data[$middle]) / 2 
        : $data[$middle];
    
    // Standard Deviation
    $variance = 0;
    foreach ($data as $value) {
        $variance += pow($value - $mean, 2);
    }
    $stdDev = sqrt($variance / $count);
    
    // Quartiles
    $q1Index = floor($count * 0.25);
    $q3Index = floor($count * 0.75);
    $q1 = $data[$q1Index];
    $q3 = $data[$q3Index];
    $iqr = $q3 - $q1;
    
    // Outliers
    $lowerBound = $q1 - (1.5 * $iqr);
    $upperBound = $q3 + (1.5 * $iqr);
    $outliers = array_filter($data, fn($v) => $v < $lowerBound || $v > $upperBound);
    
    // Percentiles
    $percentiles = [];
    for ($p = 10; $p <= 90; $p += 10) {
        $index = floor($count * ($p / 100));
        $percentiles["p$p"] = $data[$index];
    }
    
    return [
        'count' => $count,
        'min' => min($data),
        'max' => max($data),
        'mean' => round($mean, 2),
        'median' => round($median, 2),
        'std_dev' => round($stdDev, 2),
        'variance' => round($variance / $count, 2),
        'q1' => round($q1, 2),
        'q3' => round($q3, 2),
        'iqr' => round($iqr, 2),
        'outliers_count' => count($outliers),
        'percentiles' => array_map(fn($v) => round($v, 2), $percentiles),
    ];
}

// ========================================
// COMPUTATION 4: Matrix Operations
// ========================================
function generateMatrix(int $rows, int $cols): array
{
    $matrix = [];
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            $matrix[$i][$j] = rand(1, 100);
        }
    }
    return $matrix;
}

function multiplyMatrices(array $a, array $b): array
{
    $rowsA = count($a);
    $colsA = count($a[0]);
    $colsB = count($b[0]);
    
    $result = [];
    for ($i = 0; $i < $rowsA; $i++) {
        for ($j = 0; $j < $colsB; $j++) {
            $result[$i][$j] = 0;
            for ($k = 0; $k < $colsA; $k++) {
                $result[$i][$j] += $a[$i][$k] * $b[$k][$j];
            }
        }
    }
    return $result;
}

function performMatrixOperations(int $size): array
{
    $matrixA = generateMatrix($size, $size);
    $matrixB = generateMatrix($size, $size);
    
    $product = multiplyMatrices($matrixA, $matrixB);
    
    // Calculate sum and average
    $sum = 0;
    $count = 0;
    foreach ($product as $row) {
        foreach ($row as $value) {
            $sum += $value;
            $count++;
        }
    }
    
    return [
        'size' => $size,
        'total_elements' => $count,
        'sum' => $sum,
        'average' => round($sum / $count, 2),
    ];
}

// ========================================
// BENCHMARK TESTS
// ========================================

$benchmarks = [
    [
        'name' => 'Fibonacci Sequence (n=35)',
        'key' => 'fibonacci_35',
        'function' => fn() => calculateFibonacciSequence(35),
    ],
    [
        'name' => 'Prime Numbers (up to 100,000)',
        'key' => 'primes_100k',
        'function' => fn() => generatePrimes(100000),
    ],
    [
        'name' => 'Statistical Analysis (50,000 data points)',
        'key' => 'stats_50k',
        'function' => fn() => performStatisticalAnalysis(generateDataset(50000)),
    ],
    [
        'name' => 'Matrix Multiplication (100x100)',
        'key' => 'matrix_100',
        'function' => fn() => performMatrixOperations(100),
    ],
];

foreach ($benchmarks as $benchmark) {
    echo "=================================================================\n";
    echo "BENCHMARK: {$benchmark['name']}\n";
    echo "=================================================================\n\n";
    
    // WITHOUT CACHE
    echo "--- WITHOUT CACHE ---\n";
    $timesWithoutCache = [];
    
    for ($i = 1; $i <= COMPUTATION_ITERATIONS; $i++) {
        $start = microtime(true);
        $result = ($benchmark['function'])();
        $end = microtime(true);
        
        $duration = ($end - $start) * 1000;
        $timesWithoutCache[] = $duration;
        
        echo "  Run $i: " . number_format($duration, 2) . " ms\n";
    }
    
    $avgWithoutCache = array_sum($timesWithoutCache) / count($timesWithoutCache);
    echo "  Average: " . number_format($avgWithoutCache, 2) . " ms\n\n";
    
    // WITH CACHE
    echo "--- WITH CACHE ---\n";
    $cache->clear();
    
    // First call (cache miss)
    $start = microtime(true);
    $result = $cache->remember($benchmark['key'], 3600, $benchmark['function']);
    $end = microtime(true);
    $cacheMissDuration = ($end - $start) * 1000;
    
    echo "  Run 1 (cache miss): " . number_format($cacheMissDuration, 2) . " ms\n";
    
    // Subsequent calls (cache hits)
    $timesWithCache = [];
    for ($i = 2; $i <= COMPUTATION_ITERATIONS + 1; $i++) {
        $start = microtime(true);
        $result = $cache->remember($benchmark['key'], 3600, $benchmark['function']);
        $end = microtime(true);
        
        $duration = ($end - $start) * 1000;
        $timesWithCache[] = $duration;
        
        echo "  Run $i (cache hit):  " . number_format($duration, 2) . " ms\n";
    }
    
    $avgWithCache = array_sum($timesWithCache) / count($timesWithCache);
    echo "  Average (hits):  " . number_format($avgWithCache, 2) . " ms\n\n";
    
    // PERFORMANCE SUMMARY
    $speedup = $avgWithoutCache / $avgWithCache;
    $timeSaved = $avgWithoutCache - $avgWithCache;
    $percentageFaster = (($avgWithoutCache - $avgWithCache) / $avgWithoutCache) * 100;
    
    echo "--- PERFORMANCE SUMMARY ---\n";
    echo "  Without Cache: " . number_format($avgWithoutCache, 2) . " ms\n";
    echo "  With Cache:    " . number_format($avgWithCache, 2) . " ms\n";
    echo "  Speed Increase: " . number_format($speedup, 2) . "x faster\n";
    echo "  Time Saved:    " . number_format($timeSaved, 2) . " ms\n";
    echo "  Efficiency:    " . number_format($percentageFaster, 2) . "% faster\n";
    
    // Show result sample
    if (is_array($result)) {
        $dataSize = strlen(serialize($result));
        echo "  Cached Size:   " . number_format($dataSize / 1024, 2) . " KB\n";
        
        if (isset($result['count'])) {
            echo "  Result Count:  " . number_format($result['count']) . " items\n";
        } elseif (is_array($result) && !isset($result[0])) {
            echo "  Result Keys:   " . implode(', ', array_keys(array_slice($result, 0, 5))) . "...\n";
        } else {
            echo "  Result Items:  " . count($result) . " items\n";
        }
    }
    
    echo "\n";
}

// ========================================
// OVERALL SUMMARY
// ========================================
echo "=================================================================\n";
echo "OVERALL SUMMARY\n";
echo "=================================================================\n\n";

echo "All computations show significant performance improvements with caching:\n\n";

echo "Key Benefits:\n";
echo "  ✓ Eliminates redundant expensive calculations\n";
echo "  ✓ Reduces CPU usage and server load\n";
echo "  ✓ Improves response times for end users\n";
echo "  ✓ Enables handling of more concurrent requests\n";
echo "  ✓ Reduces energy consumption and costs\n\n";

echo "Best Practices:\n";
echo "  • Cache results of expensive computations\n";
echo "  • Set appropriate TTL based on data volatility\n";
echo "  • Use cache keys that reflect input parameters\n";
echo "  • Monitor cache hit rates for optimization\n";
echo "  • Clear cache when underlying data changes\n\n";

echo "=================================================================\n";
echo "CONCLUSION: Caching transforms expensive operations into instant lookups!\n";
echo "=================================================================\n";

// Cleanup
$cache->clear();
