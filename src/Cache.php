<?php

declare(strict_types=1);

namespace TweekersNut\Cache;

use TweekersNut\Cache\Contracts\CacheInterface;

class Cache
{
    private static ?CacheManager $instance = null;

    public static function make(array $config = []): CacheManager
    {
        return new CacheManager($config);
    }

    public static function instance(array $config = []): CacheManager
    {
        if (self::$instance === null) {
            self::$instance = new CacheManager($config);
        }

        return self::$instance;
    }

    public static function setInstance(CacheManager $manager): void
    {
        self::$instance = $manager;
    }

    public static function driver(?string $driver = null): CacheInterface
    {
        return self::instance()->driver($driver);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::instance()->get($key, $default);
    }

    public static function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        return self::instance()->set($key, $value, $ttl);
    }

    public static function has(string $key): bool
    {
        return self::instance()->has($key);
    }

    public static function delete(string $key): bool
    {
        return self::instance()->delete($key);
    }

    public static function clear(): bool
    {
        return self::instance()->clear();
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        return self::instance()->remember($key, $ttl, $callback);
    }

    public static function increment(string $key, int $value = 1): int
    {
        return self::instance()->increment($key, $value);
    }

    public static function decrement(string $key, int $value = 1): int
    {
        return self::instance()->decrement($key, $value);
    }

    public static function __callStatic(string $method, array $parameters)
    {
        return self::instance()->$method(...$parameters);
    }
}
