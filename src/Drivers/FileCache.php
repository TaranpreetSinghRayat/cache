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

    public function __construct(array $config = [])
    {
        $this->cachePath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $config['path'] ?? sys_get_temp_dir() . '/tweekersnut-cache'), DIRECTORY_SEPARATOR);
        $this->prefix = $config['prefix'] ?? '';
        $this->defaultTtl = $config['ttl'] ?? null;

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
        $file = $this->getFilePath($key);

        clearstatcache(true, $file);
        if (!file_exists($file)) {
            return $default;
        }

        try {
            $content = file_get_contents($file);
            if ($content === false) {
                return $default;
            }

            $data = unserialize($content);

            if (!is_array($data) || !array_key_exists('expires', $data) || !array_key_exists('value', $data)) {
                $this->delete($key);
                return $default;
            }

            if ($data['expires'] !== null && $data['expires'] < time()) {
                $this->delete($key);
                return $default;
            }

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

        try {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            return file_put_contents($file, serialize($data), LOCK_EX) !== false;
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
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return true;
        }

        try {
            return @unlink($file);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function clear(): bool
    {
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
        $hash = md5($this->prefix . $key);
        $subdir = substr($hash, 0, 2);
        return $this->cachePath . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $hash . '.cache';
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
