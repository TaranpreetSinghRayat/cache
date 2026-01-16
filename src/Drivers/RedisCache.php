<?php

declare(strict_types=1);

namespace TweekersNut\Cache\Drivers;

use Redis;
use TweekersNut\Cache\Contracts\CacheInterface;
use TweekersNut\Cache\Exceptions\CacheException;

class RedisCache implements CacheInterface
{
    private Redis $redis;
    private string $prefix;
    private ?int $defaultTtl;

    public function __construct(array $config = [])
    {
        $this->prefix = $config['prefix'] ?? '';
        $this->defaultTtl = $config['ttl'] ?? null;

        try {
            $this->redis = new Redis();
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 6379;
            $timeout = $config['timeout'] ?? 2.5;

            if (!$this->redis->connect($host, $port, $timeout)) {
                throw new CacheException("Failed to connect to Redis at {$host}:{$port}");
            }

            if (isset($config['password']) && !empty($config['password'])) {
                $this->redis->auth($config['password']);
            }

            if (isset($config['database'])) {
                $this->redis->select((int)$config['database']);
            }
        } catch (\Exception $e) {
            throw new CacheException("Redis connection error: " . $e->getMessage(), 0, $e);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $value = $this->redis->get($this->prefixKey($key));
            
            if ($value === false) {
                return $default;
            }

            return $this->unserialize($value);
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        try {
            $ttl = $ttl ?? $this->defaultTtl;
            $serialized = $this->serialize($value);

            if ($ttl === null) {
                return $this->redis->set($this->prefixKey($key), $serialized);
            }

            return $this->redis->setex($this->prefixKey($key), $ttl, $serialized);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function has(string $key): bool
    {
        try {
            return $this->redis->exists($this->prefixKey($key)) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        try {
            return $this->redis->del($this->prefixKey($key)) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function clear(): bool
    {
        try {
            if (empty($this->prefix)) {
                return $this->redis->flushDB();
            }

            $keys = $this->redis->keys($this->prefix . '*');
            if (empty($keys)) {
                return true;
            }

            return $this->redis->del($keys) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    public function increment(string $key, int $value = 1): int
    {
        try {
            return (int)$this->redis->incrBy($this->prefixKey($key), $value);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function decrement(string $key, int $value = 1): int
    {
        try {
            return (int)$this->redis->decrBy($this->prefixKey($key), $value);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function tags(array $tags): self
    {
        return $this;
    }

    private function prefixKey(string $key): string
    {
        return $this->prefix . $key;
    }

    private function serialize(mixed $value): string
    {
        return serialize($value);
    }

    private function unserialize(string $value): mixed
    {
        return unserialize($value);
    }

    public function __destruct()
    {
        try {
            if (isset($this->redis)) {
                $this->redis->close();
            }
        } catch (\Exception $e) {
        }
    }
}
