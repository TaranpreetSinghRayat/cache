<?php

declare(strict_types=1);

namespace TweekersNut\Cache;

use TweekersNut\Cache\Contracts\CacheInterface;
use TweekersNut\Cache\Drivers\ArrayCache;
use TweekersNut\Cache\Drivers\FileCache;
use TweekersNut\Cache\Drivers\RedisCache;
use TweekersNut\Cache\Drivers\SessionCache;
use TweekersNut\Cache\Exceptions\CacheException;

class CacheManager
{
    private array $config;
    private ?CacheInterface $driver = null;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'driver' => 'file',
            'prefix' => '',
            'ttl' => null,
        ], $config);
    }

    public function driver(?string $driver = null): CacheInterface
    {
        $driver = $driver ?? $this->config['driver'];

        if ($this->driver !== null && $this->config['driver'] === $driver) {
            return $this->driver;
        }

        $this->driver = $this->createDriver($driver);

        return $this->driver;
    }

    private function createDriver(string $driver): CacheInterface
    {
        return match ($driver) {
            'redis' => $this->createRedisDriver(),
            'file' => $this->createFileDriver(),
            'session' => $this->createSessionDriver(),
            'array', 'memory' => $this->createArrayDriver(),
            default => throw new CacheException("Unsupported cache driver: {$driver}"),
        };
    }

    private function createRedisDriver(): RedisCache
    {
        if (!extension_loaded('redis')) {
            throw new CacheException("Redis extension is not installed. Please install php-redis extension.");
        }

        return new RedisCache([
            'host' => $this->config['host'] ?? '127.0.0.1',
            'port' => $this->config['port'] ?? 6379,
            'password' => $this->config['password'] ?? null,
            'database' => $this->config['database'] ?? 0,
            'timeout' => $this->config['timeout'] ?? 2.5,
            'prefix' => $this->config['prefix'] ?? '',
            'ttl' => $this->config['ttl'] ?? null,
        ]);
    }

    private function createFileDriver(): FileCache
    {
        return new FileCache([
            'path' => $this->config['path'] ?? sys_get_temp_dir() . '/tweekersnut-cache',
            'prefix' => $this->config['prefix'] ?? '',
            'ttl' => $this->config['ttl'] ?? null,
        ]);
    }

    private function createSessionDriver(): SessionCache
    {
        return new SessionCache([
            'prefix' => $this->config['prefix'] ?? '',
            'ttl' => $this->config['ttl'] ?? null,
        ]);
    }

    private function createArrayDriver(): ArrayCache
    {
        return new ArrayCache([
            'prefix' => $this->config['prefix'] ?? '',
            'ttl' => $this->config['ttl'] ?? null,
        ]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver()->get($key, $default);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        return $this->driver()->set($key, $value, $ttl);
    }

    public function has(string $key): bool
    {
        return $this->driver()->has($key);
    }

    public function delete(string $key): bool
    {
        return $this->driver()->delete($key);
    }

    public function clear(): bool
    {
        return $this->driver()->clear();
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return $this->driver()->remember($key, $ttl, $callback);
    }

    public function increment(string $key, int $value = 1): int
    {
        return $this->driver()->increment($key, $value);
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->driver()->decrement($key, $value);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
