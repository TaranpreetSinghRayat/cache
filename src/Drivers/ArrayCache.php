<?php

declare(strict_types=1);

namespace TweekersNut\Cache\Drivers;

use TweekersNut\Cache\Contracts\CacheInterface;

class ArrayCache implements CacheInterface
{
    private array $storage = [];
    private string $prefix;
    private ?int $defaultTtl;

    public function __construct(array $config = [])
    {
        $this->prefix = $config['prefix'] ?? '';
        $this->defaultTtl = $config['ttl'] ?? null;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $prefixedKey = $this->prefixKey($key);

        if (!isset($this->storage[$prefixedKey])) {
            return $default;
        }

        $data = $this->storage[$prefixedKey];

        if ($data['expires'] !== null && $data['expires'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $prefixedKey = $this->prefixKey($key);
        $ttl = $ttl ?? $this->defaultTtl;

        $this->storage[$prefixedKey] = [
            'expires' => $ttl !== null ? time() + $ttl : null,
            'value' => $value,
        ];

        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function delete(string $key): bool
    {
        $prefixedKey = $this->prefixKey($key);
        unset($this->storage[$prefixedKey]);
        return true;
    }

    public function clear(): bool
    {
        if (empty($this->prefix)) {
            $this->storage = [];
            return true;
        }

        foreach (array_keys($this->storage) as $key) {
            if (str_starts_with($key, $this->prefix)) {
                unset($this->storage[$key]);
            }
        }

        return true;
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
        $current = (int)$this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    public function decrement(string $key, int $value = 1): int
    {
        $current = (int)$this->get($key, 0);
        $new = $current - $value;
        $this->set($key, $new);
        return $new;
    }

    public function cleanExpired(): int
    {
        $count = 0;

        foreach ($this->storage as $key => $data) {
            if ($data['expires'] !== null && $data['expires'] < time()) {
                unset($this->storage[$key]);
                $count++;
            }
        }

        return $count;
    }

    public function getAll(): array
    {
        return $this->storage;
    }

    private function prefixKey(string $key): string
    {
        return $this->prefix . $key;
    }
}
