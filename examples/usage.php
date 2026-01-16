<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

echo "=== TweekersNut Cache Library - Usage Examples ===\n\n";

echo "1. File Cache Driver\n";
echo "--------------------\n";
$fileCache = Cache::make([
    'driver' => 'file',
    'path' => __DIR__ . '/cache',
    'prefix' => 'myapp:',
    'ttl' => 3600
]);

$fileCache->set('user:1', ['name' => 'John Doe', 'email' => 'john@example.com'], 600);
$user = $fileCache->get('user:1');
echo "Cached user: " . json_encode($user) . "\n";
echo "Has key 'user:1': " . ($fileCache->has('user:1') ? 'Yes' : 'No') . "\n\n";

echo "2. Array (Memory) Cache Driver\n";
echo "-------------------------------\n";
$arrayCache = Cache::make(['driver' => 'array']);

$arrayCache->set('counter', 10);
echo "Initial counter: " . $arrayCache->get('counter') . "\n";
echo "After increment: " . $arrayCache->increment('counter', 5) . "\n";
echo "After decrement: " . $arrayCache->decrement('counter', 3) . "\n\n";

echo "3. Remember Method (Cache or Execute)\n";
echo "--------------------------------------\n";
$result = $arrayCache->remember('expensive_query', 300, function() {
    echo "Executing expensive operation...\n";
    sleep(1);
    return ['data' => 'This was computed', 'timestamp' => time()];
});
echo "First call result: " . json_encode($result) . "\n";

$result2 = $arrayCache->remember('expensive_query', 300, function() {
    echo "This should NOT execute (cached)\n";
    return ['data' => 'New data'];
});
echo "Second call result (from cache): " . json_encode($result2) . "\n\n";

echo "4. Static Facade Usage\n";
echo "-----------------------\n";
Cache::setInstance(Cache::make(['driver' => 'array', 'prefix' => 'app:']));

Cache::set('settings', ['theme' => 'dark', 'lang' => 'en']);
echo "Settings: " . json_encode(Cache::get('settings')) . "\n";

Cache::set('views', 100);
Cache::increment('views', 50);
echo "Total views: " . Cache::get('views') . "\n\n";

echo "5. Database Query Caching Example\n";
echo "----------------------------------\n";
function getUsersFromDatabase() {
    echo "Fetching from database...\n";
    return [
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
        ['id' => 3, 'name' => 'Charlie']
    ];
}

$users = Cache::remember('all_users', 600, fn() => getUsersFromDatabase());
echo "Users: " . json_encode($users) . "\n";

$usersCached = Cache::remember('all_users', 600, fn() => getUsersFromDatabase());
echo "Users (cached, no DB hit): " . json_encode($usersCached) . "\n\n";

echo "6. API Response Caching Example\n";
echo "--------------------------------\n";
function fetchApiData() {
    echo "Calling external API...\n";
    return [
        'status' => 'success',
        'data' => ['temperature' => 72, 'condition' => 'sunny']
    ];
}

$weather = Cache::remember('weather_data', 1800, fn() => fetchApiData());
echo "Weather: " . json_encode($weather) . "\n\n";

echo "7. Cache Deletion\n";
echo "-----------------\n";
Cache::set('temp_data', 'This will be deleted');
echo "Before delete: " . (Cache::has('temp_data') ? 'Exists' : 'Not found') . "\n";
Cache::delete('temp_data');
echo "After delete: " . (Cache::has('temp_data') ? 'Exists' : 'Not found') . "\n\n";

echo "8. Redis Cache Driver (if available)\n";
echo "------------------------------------\n";
if (extension_loaded('redis')) {
    try {
        $redisCache = Cache::make([
            'driver' => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
            'prefix' => 'myapp:',
            'ttl' => 3600
        ]);
        
        $redisCache->set('redis_test', 'Hello from Redis!');
        echo "Redis value: " . $redisCache->get('redis_test') . "\n";
        echo "Redis is working!\n";
    } catch (\Exception $e) {
        echo "Redis not available: " . $e->getMessage() . "\n";
    }
} else {
    echo "Redis extension not installed\n";
}

echo "\n=== Examples Complete ===\n";
