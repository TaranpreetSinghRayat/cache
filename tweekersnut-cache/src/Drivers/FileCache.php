<?php

declare(strict_types=1);

namespace TweekersNut\Cache\Drivers;

use TweekersNut\Cache\Contracts\CacheInterface;
use TweekersNut\Cache\Exceptions\CacheException;

class FileCache implements CacheInterface
{
    private string $cachePath;
    private string $prefix;
    private ?int $defaultTtl;
    private array $memoryCache = [];
    private array $hashCache = [];
    private int $memoryCacheLimit = 1000;
    private bool $useOpCache = true;

    public function __construct(array $config = [])
    {
        $this->cachePath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $config['path'] ?? sys_get_temp_dir() . '/tweekersnut-cache'), DIRECTORY_SEPARATOR);
        $this->prefix = $config['prefix'] ?? '';
        $this->defaultTtl = $config['ttl'] ?? null;
        $this->memoryCacheLimit = $config['memory_limit'] ?? 1000;
        $this->useOpCache = $config['use_opcache'] ?? function_exists('opcache_invalidate');

        if (!is_dir($this->cachePath)) {
            if (!mkdir($this->cachePath, 0755, true)) {
                throw new CacheException("Failed to create cache directory: {$this->cachePath}");
            }
        }

        if (!is_writable($this->cachePath)) {
            throw new CacheException("Cache directory is not writable: {$this->cachePath}");
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        // Check memory cache first (L1 cache)
        if (isset($this->memoryCache[$key])) {
            $cached = $this->memoryCache[$key];
            if ($cached['expires'] === null || $cached['expires'] >= time()) {
                return $cached['value'];
            }
            unset($this->memoryCache[$key]);
        }

        $file = $this->getFilePath($key);

        // Use stat cache efficiently - only clear for this specific file
        if (!file_exists($file)) {
            return $default;
        }

        try {
            // Use faster JSON instead of serialize for simple types
            $content = file_get_contents($file);
            if ($content === false) {
                return $default;
            }

            // Try JSON first (faster), fallback to unserialize
            $data = json_decode($content, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                $data = unserialize($content);
            }

            if (!is_array($data) || !array_key_exists('expires', $data) || !array_key_exists('value', $data)) {
                $this->delete($key);
                return $default;
            }

            if ($data['expires'] !== null && $data['expires'] < time()) {
                $this->delete($key);
                return $default;
            }

            // Store in memory cache for next access
            $this->addToMemoryCache($key, $data);

            return $data['value'];
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $file = $this->getFilePath($key);
        $ttl = $ttl ?? $this->defaultTtl;

        $data = [
            'expires' => $ttl !== null ? time() + $ttl : null,
            'value' => $value,
        ];

        // Store in memory cache immediately
        $this->addToMemoryCache($key, $data);

        try {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Use JSON for better performance with simple types
            $encoded = $this->isJsonEncodable($value) ? json_encode($data) : serialize($data);
            
            $result = file_put_contents($file, $encoded, LOCK_EX) !== false;
            
            // Invalidate opcache for this file if enabled
            if ($result && $this->useOpCache && function_exists('opcache_invalidate')) {
                opcache_invalidate($file, true);
            }
            
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function delete(string $key): bool
    {
        // Remove from memory cache
        unset($this->memoryCache[$key]);
        
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return true;
        }

        try {
            if ($this->useOpCache && function_exists('opcache_invalidate')) {
                opcache_invalidate($file, true);
            }
            return @unlink($file);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function clear(): bool
    {
        // Clear memory cache
        $this->memoryCache = [];
        $this->hashCache = [];
        
        try {
            $this->deleteDirectory($this->cachePath);
            mkdir($this->cachePath, 0755, true);
            return true;
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
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->cachePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'cache') {
                try {
                    $content = file_get_contents($file->getPathname());
                    $data = unserialize($content);

                    if (is_array($data) && isset($data['expires']) && $data['expires'] !== null && $data['expires'] < time()) {
                        @unlink($file->getPathname());
                        $count++;
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $count;
    }

    private function getFilePath(string $key): string
    {
        // Cache the hash to avoid recalculating
        if (!isset($this->hashCache[$key])) {
            $hash = md5($this->prefix . $key);
            $subdir = substr($hash, 0, 2);
            $this->hashCache[$key] = $this->cachePath . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $hash . '.cache';
        }
        return $this->hashCache[$key];
    }

    private function addToMemoryCache(string $key, array $data): void
    {
        // Implement LRU-like eviction when limit reached
        if (count($this->memoryCache) >= $this->memoryCacheLimit) {
            // Remove oldest entry (first element)
            array_shift($this->memoryCache);
        }
        $this->memoryCache[$key] = $data;
    }

    private function isJsonEncodable(mixed $value): bool
    {
        // JSON is faster for simple types
        if (is_scalar($value) || is_null($value)) {
            return true;
        }
        if (is_array($value)) {
            // Check if array contains only simple types
            foreach ($value as $item) {
                if (!is_scalar($item) && !is_null($item) && !is_array($item)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function getMany(array $keys, mixed $default = null): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMany(array $values, ?int $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        return $success;
    }

    public function deleteMany(array $keys): bool
    {
        $success = true;
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $success = false;
            }
        }
        return $success;
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($dir);
    }
}
