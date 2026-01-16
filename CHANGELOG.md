# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-17

### Added
- Initial release of TweekersNut Cache
- Redis cache driver with full TTL support
- File cache driver with directory-based storage
- Session cache driver for per-user caching
- Array (in-memory) cache driver for testing
- Unified `CacheInterface` for all drivers
- `CacheManager` for dynamic driver loading
- Static `Cache` facade for convenient access
- `remember()` method for cache-or-execute pattern
- `increment()` and `decrement()` methods for atomic operations
- Automatic serialization/unserialization
- Prefix/namespace support
- Exception-safe operations
- PSR-4 autoloading
- Comprehensive documentation
- Usage examples for database and API caching
- PHPUnit test structure
- MIT License

### Features
- PHP 8.0+ type hints throughout
- Zero framework dependencies
- Production-ready performance optimizations
- Graceful fallback handling
- Configurable TTL per operation
- Clean, readable code following PSR-12

[1.0.0]: https://github.com/tweekersnut/cache/releases/tag/v1.0.0
