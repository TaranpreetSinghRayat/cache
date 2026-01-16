# TweekersNut Cache

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**A high-performance, pluggable PHP caching library with Redis, file, session, and memory drivers.**

TweekersNut Cache is a professional, production-ready caching solution designed to speed up backend processing, data fetching, and manipulation. It provides a unified interface for multiple cache drivers, making it easy to switch between different caching strategies without changing your code.

---

## 🚀 Features

- **Multiple Cache Drivers**: Redis, File, Session, and Array (in-memory)
- **Unified Interface**: Consistent API across all drivers
- **PSR-4 Autoloading**: Modern PHP standards
- **Type-Safe**: Full PHP 8.0+ type hints
- **Zero Dependencies**: No framework required (works standalone)
- **High Performance**: Optimized for production environments
- **TTL Support**: Time-to-live for automatic expiration
- **Prefix/Namespace Support**: Avoid key collisions
- **Remember Pattern**: Cache-or-execute in one call
- **Increment/Decrement**: Atomic counter operations
- **Exception-Safe**: Never breaks application flow

---

## 📦 Installation

Install via Composer:

```bash
composer require tweekersnut/cache
```

### Requirements

- PHP >= 8.0
- (Optional) Redis extension for Redis driver
- (Optional) Predis library as alternative to Redis extension

---

## 🎯 Quick Start

### Basic Usage

```php
<?php

require 'vendor/autoload.php';

use TweekersNut\Cache\Cache;

// Create a cache instance
$cache = Cache::make([
    'driver' => 'file',
    'path' => '/path/to/cache',
    'prefix' => 'myapp:',
    'ttl' => 3600
]);

// Store data
$cache->set('user:1', ['name' => 'John Doe', 'email' => 'john@example.com'], 600);

// Retrieve data
$user = $cache->get('user:1');

// Check if key exists
if ($cache->has('user:1')) {
    echo "User found in cache!";
}

// Delete a key
$cache->delete('user:1');

// Clear all cache
$cache->clear();
```

### Static Facade Usage

```php
use TweekersNut\Cache\Cache;

// Configure once
Cache::setInstance(Cache::make(['driver' => 'redis', 'host' => '127.0.0.1']));

// Use anywhere
Cache::set('key', 'value', 300);
$value = Cache::get('key');
```

---

## 🔧 Configuration

### Redis Driver

```php
$cache = Cache::make([
    'driver' => 'redis',
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => 'secret',      // Optional
    'database' => 0,             // Optional (default: 0)
    'timeout' => 2.5,            // Optional (default: 2.5)
    'prefix' => 'myapp:',        // Optional
    'ttl' => 3600                // Optional default TTL
]);
```

**Requirements**: PHP Redis extension (`ext-redis`) or Predis library

**Best For**: High-traffic applications, distributed systems, shared cache across servers

### File Cache Driver

```php
$cache = Cache::make([
    'driver' => 'file',
    'path' => '/var/www/cache',  // Cache directory
    'prefix' => 'myapp:',        // Optional
    'ttl' => 3600                // Optional default TTL
]);
```

**Best For**: Single-server applications, development, when Redis is unavailable

### Session Cache Driver

```php
$cache = Cache::make([
    'driver' => 'session',
    'prefix' => 'cache:',        // Optional
    'ttl' => 1800                // Optional default TTL
]);
```

**Best For**: Per-user caching, temporary user-specific data

### Array (Memory) Cache Driver

```php
$cache = Cache::make([
    'driver' => 'array',
    'prefix' => 'temp:',         // Optional
    'ttl' => 300                 // Optional default TTL
]);
```

**Best For**: Testing, single-request caching, temporary runtime data

---

## 📚 API Reference

### Core Methods

#### `get(string $key, mixed $default = null): mixed`

Retrieve a value from cache.

```php
$user = $cache->get('user:1');
$user = $cache->get('user:1', ['name' => 'Guest']); // With default
```

#### `set(string $key, mixed $value, ?int $ttl = null): bool`

Store a value in cache.

```php
$cache->set('user:1', $userData, 600); // Expires in 600 seconds
$cache->set('permanent', $data);       // No expiration
```

#### `has(string $key): bool`

Check if a key exists in cache.

```php
if ($cache->has('user:1')) {
    // Key exists
}
```

#### `delete(string $key): bool`

Remove a key from cache.

```php
$cache->delete('user:1');
```

#### `clear(): bool`

Clear all cache entries (respects prefix).

```php
$cache->clear();
```

#### `remember(string $key, int $ttl, callable $callback): mixed`

Get from cache or execute callback and store result.

```php
$users = $cache->remember('all_users', 600, function() {
    return User::all(); // Heavy database query
});
```

#### `increment(string $key, int $value = 1): int`

Increment a numeric value.

```php
$views = $cache->increment('page_views');
$views = $cache->increment('page_views', 10);
```

#### `decrement(string $key, int $value = 1): int`

Decrement a numeric value.

```php
$remaining = $cache->decrement('api_quota');
```

---

## 💡 Usage Examples

### Caching Database Queries

```php
use TweekersNut\Cache\Cache;

function getUsers() {
    return Cache::remember('users:all', 600, function() {
        // This query only runs if cache is empty
        return $db->query("SELECT * FROM users")->fetchAll();
    });
}
```

### Caching API Responses

```php
function getWeatherData($city) {
    $key = "weather:{$city}";
    
    return Cache::remember($key, 1800, function() use ($city) {
        // API call only happens if not cached
        $response = file_get_contents("https://api.weather.com/{$city}");
        return json_decode($response, true);
    });
}
```

### Rate Limiting

```php
function checkRateLimit($userId) {
    $key = "rate_limit:{$userId}";
    $requests = (int) Cache::get($key, 0);
    
    if ($requests >= 100) {
        throw new Exception("Rate limit exceeded");
    }
    
    Cache::increment($key);
    
    if ($requests === 0) {
        Cache::set($key, 1, 3600); // Reset after 1 hour
    }
}
```

### Caching Heavy Computations

```php
function calculateStatistics() {
    return Cache::remember('daily_stats', 86400, function() {
        // Expensive computation
        $stats = [
            'total_users' => countUsers(),
            'total_orders' => countOrders(),
            'revenue' => calculateRevenue()
        ];
        return $stats;
    });
}
```

### Session-Based User Preferences

```php
$sessionCache = Cache::make(['driver' => 'session']);

// Store user preferences
$sessionCache->set('user_theme', 'dark');
$sessionCache->set('user_language', 'en');

// Retrieve
$theme = $sessionCache->get('user_theme', 'light');
```

---

## 🔄 Driver Comparison

| Feature | Redis | File | Session | Array |
|---------|-------|------|---------|-------|
| **Persistence** | ✅ Yes | ✅ Yes | ⚠️ Session only | ❌ No |
| **Multi-Server** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **Performance** | ⚡ Excellent | ⚠️ Good | ⚠️ Good | ⚡ Excellent |
| **Setup Required** | ✅ Redis server | ❌ None | ❌ None | ❌ None |
| **Memory Usage** | ⚠️ External | ⚠️ Disk | ⚠️ Session | ⚠️ PHP memory |
| **Best For** | Production | Development | User data | Testing |

---

## 🎨 Advanced Features

### Prefix/Namespacing

Avoid key collisions by using prefixes:

```php
$cache = Cache::make([
    'driver' => 'redis',
    'prefix' => 'myapp:v1:'
]);

$cache->set('users', $data); // Actually stored as "myapp:v1:users"
```

### Multiple Cache Instances

```php
// Fast cache for frequent access
$fastCache = Cache::make(['driver' => 'redis']);

// Persistent cache for less frequent data
$persistentCache = Cache::make(['driver' => 'file']);

$fastCache->set('hot_data', $data, 60);
$persistentCache->set('cold_data', $data, 86400);
```

### Graceful Fallback

```php
try {
    $cache = Cache::make(['driver' => 'redis']);
} catch (\Exception $e) {
    // Fallback to file cache if Redis unavailable
    $cache = Cache::make(['driver' => 'file']);
}
```

---

## 🏆 Best Practices

1. **Use appropriate TTL**: Set reasonable expiration times to balance freshness and performance
2. **Prefix your keys**: Avoid collisions, especially in shared environments
3. **Cache expensive operations**: Database queries, API calls, complex calculations
4. **Use `remember()` pattern**: Simplifies cache-or-execute logic
5. **Handle cache failures gracefully**: Never let cache issues break your app
6. **Monitor cache hit rates**: Optimize your caching strategy based on metrics
7. **Clear cache strategically**: Invalidate when data changes, not on every request

---

## 🧪 Testing

The library includes a test suite. Run tests with PHPUnit:

```bash
composer install --dev
vendor/bin/phpunit
```

---

## 📄 License

This library is open-source software licensed under the [MIT License](LICENSE).

---

## 👥 Author

**TweekersNut Network**

- Website: [https://tweekersnut.com](https://tweekersnut.com)
- Email: dev@tweekersnut.com

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 🐛 Support

If you encounter any issues or have questions:

- Open an issue on GitHub
- Email: dev@tweekersnut.com

---

## 🎯 Roadmap

- [ ] PSR-6 and PSR-16 compliance
- [ ] Memcached driver
- [ ] Tag-based cache invalidation
- [ ] Cache warming utilities
- [ ] Performance benchmarking tools
- [ ] Laravel service provider
- [ ] Symfony bundle

---

**Made with ❤️ by TweekersNut Network**
