<?php

declare(strict_types=1);

namespace TweekersNut\Cache\Drivers;

use TweekersNut\Cache\Contracts\CacheInterface;
use TweekersNut\Cache\Exceptions\CacheException;

class SessionCache implements CacheInterface
{
    private string $prefix;
    private ?int $defaultTtl;
    private string $storageKey = '_tweekersnut_cache';

    public function __construct(array $config = [])
    {
        $this->prefix = $config['prefix'] ?? '';
        $this->defaultTtl = $config['ttl'] ?? null;

        if (session_status() === PHP_SESSION_NONE) {
            if (!session_start()) {
                throw new CacheException("Failed to start session for SessionCache");
            }
        }

        if (!isset($_SESSION[$this->storageKey])) {
            $_SESSION[$this->storageKey] = [];
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $prefixedKey = $this->prefixKey($key);

        if (!isset($_SESSION[$this->storageKey][$prefixedKey])) {
            return $default;
        }

        $data = $_SESSION[$this->storageKey][$prefixedKey];

        if (!is_array($data) || !isset($data['expires'], $data['value'])) {
            $this->delete($key);
            return $default;
        }

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

        $_SESSION[$this->storageKey][$prefixedKey] = [
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
        unset($_SESSION[$this->storageKey][$prefixedKey]);
        return true;
    }

    public function clear(): bool
    {
        if (empty($this->prefix)) {
            $_SESSION[$this->storageKey] = [];
            return true;
        }

        foreach (array_keys($_SESSION[$this->storageKey]) as $key) {
            if (str_starts_with($key, $this->prefix)) {
                unset($_SESSION[$this->storageKey][$key]);
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

        foreach ($_SESSION[$this->storageKey] as $key => $data) {
            if (is_array($data) && isset($data['expires']) && $data['expires'] !== null && $data['expires'] < time()) {
                unset($_SESSION[$this->storageKey][$key]);
                $count++;
            }
        }

        return $count;
    }

    private function prefixKey(string $key): string
    {
        return $this->prefix . $key;
    }
}
