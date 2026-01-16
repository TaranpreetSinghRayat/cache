<?php

/**
 * Comprehensive Benchmark Runner
 * 
 * Runs all benchmark examples and provides a consolidated performance report
 * demonstrating the overall impact of caching on application performance.
 */

require __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

echo "\n";
echo "█████████████████████████████████████████████████████████████████\n";
echo "█                                                               █\n";
echo "█         TWEEKERSNUT CACHE - COMPREHENSIVE BENCHMARK          █\n";
echo "█                                                               █\n";
echo "█████████████████████████████████████████████████████████████████\n";
echo "\n";

$startTime = microtime(true);

// Test different cache drivers
$drivers = [
    'array' => [
        'name' => 'Array (In-Memory)',
        'config' => ['driver' => 'array', 'prefix' => 'bench:'],
    ],
    'file' => [
        'name' => 'File System',
        'config' => [
            'driver' => 'file',
            'path' => sys_get_temp_dir() . '/cache-benchmark',
            'prefix' => 'bench:',
        ],
    ],
];

echo "Testing Cache Drivers: " . implode(', ', array_column($drivers, 'name')) . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Start Time: " . date('Y-m-d H:i:s') . "\n\n";

$results = [];

foreach ($drivers as $driverKey => $driverInfo) {
    echo "=================================================================\n";
    echo "TESTING DRIVER: {$driverInfo['name']}\n";
    echo "=================================================================\n\n";
    
    $cache = Cache::make($driverInfo['config']);
    $cache->clear();
    
    $driverResults = [];
    
    // ========================================
    // TEST 1: Simple Key-Value Operations
    // ========================================
    echo "Test 1: Simple Key-Value Operations\n";
    echo "------------------------------------\n";
    
    $iterations = 1000;
    
    // Write performance
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->set("key_$i", "value_$i", 3600);
    }
    $writeTime = (microtime(true) - $start) * 1000;
    
    // Read performance (cache hits)
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->get("key_$i");
    }
    $readTime = (microtime(true) - $start) * 1000;
    
    // Delete performance
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->delete("key_$i");
    }
    $deleteTime = (microtime(true) - $start) * 1000;
    
    $driverResults['simple_operations'] = [
        'iterations' => $iterations,
        'write_time' => $writeTime,
        'read_time' => $readTime,
        'delete_time' => $deleteTime,
        'write_ops_per_sec' => round($iterations / ($writeTime / 1000)),
        'read_ops_per_sec' => round($iterations / ($readTime / 1000)),
    ];
    
    echo "  Iterations: " . number_format($iterations) . "\n";
    echo "  Write Time: " . number_format($writeTime, 2) . " ms (" . 
         number_format($driverResults['simple_operations']['write_ops_per_sec']) . " ops/sec)\n";
    echo "  Read Time:  " . number_format($readTime, 2) . " ms (" . 
         number_format($driverResults['simple_operations']['read_ops_per_sec']) . " ops/sec)\n";
    echo "  Delete Time: " . number_format($deleteTime, 2) . " ms\n\n";
    
    // ========================================
    // TEST 2: Complex Data Structures
    // ========================================
    echo "Test 2: Complex Data Structures\n";
    echo "--------------------------------\n";
    
    $complexData = [
        'users' => array_map(fn($i) => [
            'id' => $i,
            'name' => "User $i",
            'email' => "user$i@example.com",
            'metadata' => ['key' => 'value', 'nested' => ['deep' => 'data']],
        ], range(1, 100)),
        'settings' => ['theme' => 'dark', 'language' => 'en', 'notifications' => true],
        'timestamp' => time(),
    ];
    
    $iterations = 100;
    
    // Write complex data
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->set("complex_$i", $complexData, 3600);
    }
    $complexWriteTime = (microtime(true) - $start) * 1000;
    
    // Read complex data
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->get("complex_$i");
    }
    $complexReadTime = (microtime(true) - $start) * 1000;
    
    $dataSize = strlen(serialize($complexData));
    
    $driverResults['complex_data'] = [
        'iterations' => $iterations,
        'data_size' => $dataSize,
        'write_time' => $complexWriteTime,
        'read_time' => $complexReadTime,
        'write_throughput' => round(($dataSize * $iterations) / ($complexWriteTime / 1000) / 1024, 2),
        'read_throughput' => round(($dataSize * $iterations) / ($complexReadTime / 1000) / 1024, 2),
    ];
    
    echo "  Iterations: " . number_format($iterations) . "\n";
    echo "  Data Size: " . number_format($dataSize / 1024, 2) . " KB per item\n";
    echo "  Write Time: " . number_format($complexWriteTime, 2) . " ms\n";
    echo "  Read Time:  " . number_format($complexReadTime, 2) . " ms\n";
    echo "  Write Throughput: " . number_format($driverResults['complex_data']['write_throughput'], 2) . " KB/sec\n";
    echo "  Read Throughput:  " . number_format($driverResults['complex_data']['read_throughput'], 2) . " KB/sec\n\n";
    
    // ========================================
    // TEST 3: Cache Hit vs Miss Performance
    // ========================================
    echo "Test 3: Cache Hit vs Miss Performance\n";
    echo "--------------------------------------\n";
    
    $cache->clear();
    $key = 'hit_miss_test';
    $value = str_repeat('x', 10000); // 10KB of data
    
    // Cache miss
    $start = microtime(true);
    $result = $cache->get($key, 'default');
    $missTime = (microtime(true) - $start) * 1000;
    
    // Set value
    $cache->set($key, $value, 3600);
    
    // Cache hit
    $start = microtime(true);
    $result = $cache->get($key);
    $hitTime = (microtime(true) - $start) * 1000;
    
    $driverResults['hit_miss'] = [
        'miss_time' => $missTime,
        'hit_time' => $hitTime,
        'hit_speedup' => round($missTime / $hitTime, 2),
    ];
    
    echo "  Cache Miss: " . number_format($missTime, 4) . " ms\n";
    echo "  Cache Hit:  " . number_format($hitTime, 4) . " ms\n";
    echo "  Hit Speedup: " . number_format($driverResults['hit_miss']['hit_speedup'], 2) . "x faster\n\n";
    
    // ========================================
    // TEST 4: Remember Pattern Performance
    // ========================================
    echo "Test 4: Remember Pattern (Cache-or-Execute)\n";
    echo "--------------------------------------------\n";
    
    $cache->clear();
    $iterations = 5;
    
    $expensiveOperation = function() {
        usleep(50000); // 50ms delay
        return array_map(fn($i) => ['id' => $i, 'data' => str_repeat('x', 100)], range(1, 100));
    };
    
    // First call (cache miss + execution)
    $start = microtime(true);
    $result = $cache->remember('remember_test', 3600, $expensiveOperation);
    $firstCallTime = (microtime(true) - $start) * 1000;
    
    // Subsequent calls (cache hits)
    $hitTimes = [];
    for ($i = 0; $i < $iterations; $i++) {
        $start = microtime(true);
        $result = $cache->remember('remember_test', 3600, $expensiveOperation);
        $hitTimes[] = (microtime(true) - $start) * 1000;
    }
    
    $avgHitTime = array_sum($hitTimes) / count($hitTimes);
    
    $driverResults['remember_pattern'] = [
        'first_call' => $firstCallTime,
        'avg_hit_time' => $avgHitTime,
        'speedup' => round($firstCallTime / $avgHitTime, 2),
    ];
    
    echo "  First Call (miss + execute): " . number_format($firstCallTime, 2) . " ms\n";
    echo "  Avg Hit Time: " . number_format($avgHitTime, 2) . " ms\n";
    echo "  Speedup: " . number_format($driverResults['remember_pattern']['speedup'], 2) . "x faster\n\n";
    
    // ========================================
    // TEST 5: Increment/Decrement Operations
    // ========================================
    echo "Test 5: Atomic Counter Operations\n";
    echo "-----------------------------------\n";
    
    $cache->clear();
    $iterations = 1000;
    
    // Increment performance
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->increment('counter');
    }
    $incrementTime = (microtime(true) - $start) * 1000;
    
    // Decrement performance
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $cache->decrement('counter');
    }
    $decrementTime = (microtime(true) - $start) * 1000;
    
    $driverResults['counters'] = [
        'iterations' => $iterations,
        'increment_time' => $incrementTime,
        'decrement_time' => $decrementTime,
        'increment_ops_per_sec' => round($iterations / ($incrementTime / 1000)),
        'decrement_ops_per_sec' => round($iterations / ($decrementTime / 1000)),
    ];
    
    echo "  Iterations: " . number_format($iterations) . "\n";
    echo "  Increment Time: " . number_format($incrementTime, 2) . " ms (" . 
         number_format($driverResults['counters']['increment_ops_per_sec']) . " ops/sec)\n";
    echo "  Decrement Time: " . number_format($decrementTime, 2) . " ms (" . 
         number_format($driverResults['counters']['decrement_ops_per_sec']) . " ops/sec)\n\n";
    
    $results[$driverKey] = [
        'name' => $driverInfo['name'],
        'tests' => $driverResults,
    ];
    
    $cache->clear();
}

$totalTime = (microtime(true) - $startTime) * 1000;

// ========================================
// CONSOLIDATED REPORT
// ========================================
echo "\n";
echo "█████████████████████████████████████████████████████████████████\n";
echo "█                    CONSOLIDATED REPORT                        █\n";
echo "█████████████████████████████████████████████████████████████████\n\n";

echo "Driver Performance Comparison:\n";
echo "================================\n\n";

// Compare simple operations
echo "Simple Operations (1000 iterations):\n";
echo "-------------------------------------\n";
foreach ($results as $driverKey => $driverData) {
    $ops = $driverData['tests']['simple_operations'];
    echo "{$driverData['name']}:\n";
    echo "  Write: " . number_format($ops['write_ops_per_sec']) . " ops/sec\n";
    echo "  Read:  " . number_format($ops['read_ops_per_sec']) . " ops/sec\n";
}
echo "\n";

// Compare complex data
echo "Complex Data Throughput:\n";
echo "-------------------------\n";
foreach ($results as $driverKey => $driverData) {
    $complex = $driverData['tests']['complex_data'];
    echo "{$driverData['name']}:\n";
    echo "  Write: " . number_format($complex['write_throughput'], 2) . " KB/sec\n";
    echo "  Read:  " . number_format($complex['read_throughput'], 2) . " KB/sec\n";
}
echo "\n";

// Overall statistics
echo "Overall Statistics:\n";
echo "-------------------\n";
echo "Total Benchmark Time: " . number_format($totalTime / 1000, 2) . " seconds\n";
echo "Memory Peak Usage: " . number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB\n";
echo "Tests Completed: " . (count($results) * 5) . "\n\n";

echo "Key Findings:\n";
echo "-------------\n";
echo "✓ Cache hits are significantly faster than cache misses\n";
echo "✓ Array driver offers best performance for single-request caching\n";
echo "✓ File driver provides persistent caching with good performance\n";
echo "✓ Remember pattern simplifies cache-or-execute logic\n";
echo "✓ Atomic operations (increment/decrement) are highly efficient\n\n";

echo "Recommendations:\n";
echo "----------------\n";
echo "• Use Array driver for temporary, single-request caching\n";
echo "• Use File driver for persistent caching in single-server setups\n";
echo "• Use Redis driver for distributed caching (production)\n";
echo "• Set appropriate TTL values based on data volatility\n";
echo "• Monitor cache hit rates to optimize caching strategy\n";
echo "• Use remember() pattern for expensive operations\n\n";

echo "█████████████████████████████████████████████████████████████████\n";
echo "█              BENCHMARK COMPLETED SUCCESSFULLY                 █\n";
echo "█████████████████████████████████████████████████████████████████\n\n";

echo "End Time: " . date('Y-m-d H:i:s') . "\n";
echo "Duration: " . number_format($totalTime / 1000, 2) . " seconds\n\n";
