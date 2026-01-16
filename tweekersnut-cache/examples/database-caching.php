<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TweekersNut\Cache\Cache;

echo "=== Database Query Caching Example ===\n\n";

$cache = Cache::make([
    'driver' => 'file',
    'path' => __DIR__ . '/cache',
    'prefix' => 'db:',
    'ttl' => 600
]);

class Database {
    public static function query(string $sql): array {
        echo "[DB] Executing query: {$sql}\n";
        sleep(1);
        
        return [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com']
        ];
    }
}

echo "1. First request (cache miss - hits database):\n";
$users = $cache->remember('users:all', 600, function() {
    return Database::query("SELECT * FROM users");
});
echo "Retrieved " . count($users) . " users\n";
echo json_encode($users, JSON_PRETTY_PRINT) . "\n\n";

echo "2. Second request (cache hit - no database query):\n";
$usersCached = $cache->remember('users:all', 600, function() {
    return Database::query("SELECT * FROM users");
});
echo "Retrieved " . count($usersCached) . " users (from cache)\n\n";

echo "3. Caching individual user records:\n";
function getUser(int $id, $cache): ?array {
    return $cache->remember("user:{$id}", 300, function() use ($id) {
        echo "[DB] Fetching user {$id}\n";
        sleep(1);
        return ['id' => $id, 'name' => "User {$id}", 'email' => "user{$id}@example.com"];
    });
}

$user1 = getUser(1, $cache);
echo "User 1: " . json_encode($user1) . "\n";

$user1Again = getUser(1, $cache);
echo "User 1 (cached): " . json_encode($user1Again) . "\n\n";

echo "4. Cache invalidation on update:\n";
function updateUser(int $id, array $data, $cache): void {
    echo "[DB] Updating user {$id}\n";
    $cache->delete("user:{$id}");
    $cache->delete("users:all");
    echo "Cache invalidated for user {$id}\n";
}

updateUser(1, ['name' => 'John Updated'], $cache);

echo "\n5. Caching query results with parameters:\n";
function getUsersByRole(string $role, $cache): array {
    $key = "users:role:{$role}";
    return $cache->remember($key, 600, function() use ($role) {
        echo "[DB] Fetching users with role: {$role}\n";
        sleep(1);
        return [
            ['id' => 1, 'name' => 'Admin User', 'role' => $role],
            ['id' => 2, 'name' => 'Another Admin', 'role' => $role]
        ];
    });
}

$admins = getUsersByRole('admin', $cache);
echo "Admins: " . json_encode($admins) . "\n";

$adminsAgain = getUsersByRole('admin', $cache);
echo "Admins (cached): " . json_encode($adminsAgain) . "\n\n";

echo "=== Example Complete ===\n";
