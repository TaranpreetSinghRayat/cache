# TweekersNut Cache - Performance Benchmarks

This directory contains comprehensive benchmark examples demonstrating how the TweekersNut Cache library dramatically improves application performance by reducing processing time for expensive operations.

## 📊 Available Benchmarks

### 1. Database Query Benchmark (`benchmark-database.php`)

Demonstrates caching performance for large database query results.

**Features:**
- Simulates 10,000 database records with complex data structures
- Performs data aggregation and statistical analysis
- Compares performance with and without caching

**Expected Results:**
- **Without Cache:** ~500-1000ms per query
- **With Cache:** ~0.5-2ms per query
- **Speed Increase:** 500-1000x faster

**Run:**
```bash
php examples/benchmark-database.php
```

### 2. API Response Caching Benchmark (`benchmark-api.php`)

Shows how caching eliminates API call latency and reduces external service costs.

**Features:**
- Simulates multiple API endpoints (users, products, analytics, weather)
- 500ms simulated network latency per call
- Tracks cache hits, misses, and cost savings

**Expected Results:**
- **Without Cache:** ~500ms per API call
- **With Cache:** ~0.1-1ms per cached response
- **Speed Increase:** 500-5000x faster
- **Cost Savings:** Eliminates redundant API calls

**Run:**
```bash
php examples/benchmark-api.php
```

### 3. Heavy Computation Benchmark (`benchmark-computation.php`)

Demonstrates caching benefits for CPU-intensive operations.

**Features:**
- Fibonacci sequence calculation (recursive)
- Prime number generation (up to 100,000)
- Statistical analysis (50,000 data points)
- Matrix multiplication (100x100)

**Expected Results:**
- **Fibonacci (n=35):** 100-500ms → <1ms (500x faster)
- **Prime Generation:** 500-1000ms → <1ms (1000x faster)
- **Statistical Analysis:** 50-200ms → <1ms (100x faster)
- **Matrix Operations:** 100-300ms → <1ms (200x faster)

**Run:**
```bash
php examples/benchmark-computation.php
```

### 4. Comprehensive Benchmark Suite (`benchmark-all.php`)

Runs all performance tests and provides a consolidated report.

**Features:**
- Tests multiple cache drivers (Array, File)
- Simple key-value operations (1000 iterations)
- Complex data structures (nested arrays)
- Cache hit vs miss comparison
- Remember pattern performance
- Atomic counter operations

**Expected Results:**
- **Array Driver:** 50,000+ ops/sec
- **File Driver:** 10,000+ ops/sec
- **Cache Hits:** 1000x faster than misses
- **Remember Pattern:** Eliminates redundant execution

**Run:**
```bash
php examples/benchmark-all.php
```

## 🚀 Quick Start

Run all benchmarks at once:

```bash
# Individual benchmarks
php examples/benchmark-database.php
php examples/benchmark-api.php
php examples/benchmark-computation.php

# Comprehensive suite
php examples/benchmark-all.php
```

## 📈 Performance Summary

Based on typical benchmark results:

| Scenario | Without Cache | With Cache | Speed Increase |
|----------|--------------|------------|----------------|
| Database Queries | 500-1000ms | 0.5-2ms | **500-1000x** |
| API Calls | 500ms | 0.1-1ms | **500-5000x** |
| Heavy Computation | 100-1000ms | <1ms | **100-1000x** |
| Simple Operations | N/A | 0.01-0.1ms | **10,000+ ops/sec** |

## 💡 Real-World Impact

### Example: E-commerce Product Catalog

**Scenario:** Loading 10,000 products with pricing, inventory, and reviews

- **Without Cache:** 800ms per page load
- **With Cache:** 2ms per page load
- **Result:** 400x faster, handles 400x more concurrent users

### Example: Analytics Dashboard

**Scenario:** Complex statistical calculations on 50,000 data points

- **Without Cache:** 150ms per request
- **With Cache:** 0.5ms per request
- **Result:** 300x faster, real-time dashboard updates

### Example: External API Integration

**Scenario:** Weather data from third-party API

- **Without Cache:** 500ms latency + API costs
- **With Cache:** <1ms, zero API calls for cached data
- **Result:** 500x faster + 99% cost reduction

## 🎯 Best Practices Demonstrated

1. **Use `remember()` pattern** - Simplifies cache-or-execute logic
2. **Set appropriate TTL** - Balance freshness vs performance
3. **Cache expensive operations** - Database queries, API calls, computations
4. **Monitor cache hit rates** - Optimize caching strategy
5. **Choose right driver** - Array for speed, File for persistence, Redis for production

## 📊 Interpreting Results

### What to Look For:

- **Speed Increase:** How many times faster cached operations are
- **Time Saved:** Actual milliseconds saved per operation
- **Throughput:** Operations per second (higher is better)
- **Cache Hit Rate:** Percentage of requests served from cache
- **Memory Usage:** Size of cached data

### Factors Affecting Performance:

- **Data Size:** Larger data takes longer to serialize/deserialize
- **Cache Driver:** Array > File > Redis (for single server)
- **System Resources:** CPU, RAM, disk I/O
- **PHP Version:** Newer versions generally faster
- **Data Complexity:** Deeply nested structures take longer

## 🔧 Customizing Benchmarks

You can modify the benchmarks to match your use case:

```php
// Change dataset size
define('DATASET_SIZE', 50000); // Increase for stress testing

// Change iterations
define('QUERY_ITERATIONS', 10); // More iterations = better average

// Change cache driver
$cache = Cache::make([
    'driver' => 'redis', // Test Redis performance
    'host' => '127.0.0.1',
]);

// Change TTL
$cache->remember('key', 7200, $callback); // 2 hour cache
```

## 📝 Notes

- Benchmarks use simulated data and operations
- Actual performance varies based on hardware and PHP configuration
- Results show relative performance improvements
- Production environments may see different absolute numbers
- Network latency simulation is approximate

## 🎓 Learning Resources

After running benchmarks, explore:

1. **examples/usage.php** - Basic cache usage patterns
2. **examples/database-caching.php** - Real database integration
3. **examples/api-caching.php** - External API caching
4. **README.md** - Complete library documentation

## 🤝 Contributing

Found ways to improve benchmarks? Submit a PR!

- Add new benchmark scenarios
- Improve accuracy of simulations
- Add visualization of results
- Test additional cache drivers

---

**Made with ❤️ by TweekersNut Network**
