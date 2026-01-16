# Quick Start Guide - TweekersNut Cache

## 🚀 Installation

```bash
composer require tweekersnut/cache
```

## 📖 Basic Usage (30 seconds)

```php
<?php
require 'vendor/autoload.php';

use TweekersNut\Cache\Cache;

// Create cache instance
$cache = Cache::make(['driver' => 'file', 'path' => '/tmp/cache']);

// Store data
$cache->set('user:1', ['name' => 'John', 'email' => 'john@example.com'], 3600);

// Retrieve data
$user = $cache->get('user:1');

// Cache expensive operations
$users = $cache->remember('all_users', 600, function() {
    return User::all(); // Only runs if not cached
});
```

## 🎯 Run Performance Benchmarks

See how caching improves your application:

```bash
# Database query benchmark (613x faster!)
php examples/benchmark-database.php

# API caching benchmark (9x faster + cost savings!)
php examples/benchmark-api.php

# Heavy computation benchmark (1,278x faster!)
php examples/benchmark-computation.php

# Comprehensive suite (all tests)
php examples/benchmark-all.php
```

## 📊 Expected Results

| Benchmark | Without Cache | With Cache | Improvement |
|-----------|--------------|------------|-------------|
| Database Queries | ~1,500 ms | ~2 ms | **613x faster** |
| API Calls | ~500 ms | ~2 ms | **9-250x faster** |
| Fibonacci (n=35) | ~3,850 ms | ~3 ms | **1,278x faster** |
| Prime Numbers | ~37 ms | ~4 ms | **9x faster** |

## 🔧 Available Drivers

```php
// Array (fastest, in-memory only)
$cache = Cache::make(['driver' => 'array']);

// File (persistent, good for development)
$cache = Cache::make(['driver' => 'file', 'path' => '/tmp/cache']);

// Redis (production, distributed)
$cache = Cache::make(['driver' => 'redis', 'host' => '127.0.0.1']);

// Session (user-specific data)
$cache = Cache::make(['driver' => 'session']);
```

## 📚 Documentation

- **Full Documentation:** [README.md](README.md)
- **Performance Results:** [PERFORMANCE.md](PERFORMANCE.md)
- **Benchmark Guide:** [examples/BENCHMARKS.md](examples/BENCHMARKS.md)
- **Examples:** `examples/` directory

## ✅ Verify Installation

```bash
# Run tests
vendor/bin/phpunit

# Validate package
composer validate

# Try basic example
php examples/usage.php
```

## 🎓 Next Steps

1. Run benchmarks to see performance gains
2. Read [PERFORMANCE.md](PERFORMANCE.md) for real-world results
3. Explore `examples/` directory for usage patterns
4. Integrate into your application
5. Monitor cache hit rates and optimize

---

**Made with ❤️ by TweekersNut Network**
