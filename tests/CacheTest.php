<?php

declare(strict_types=1);

namespace TweekersNut\Cache\Tests;

use PHPUnit\Framework\TestCase;
use TweekersNut\Cache\Cache;
use TweekersNut\Cache\Drivers\ArrayCache;
use TweekersNut\Cache\Drivers\FileCache;

class CacheTest extends TestCase
{
    private string $cachePath;

    protected function setUp(): void
    {
        $this->cachePath = sys_get_temp_dir() . '/tweekersnut-cache-test-' . uniqid();
    }

    protected function tearDown(): void
    {
        if (is_dir($this->cachePath)) {
            $this->deleteDirectory($this->cachePath);
        }
    }

    public function testArrayCacheBasicOperations(): void
    {
        $cache = new ArrayCache(['prefix' => 'test:']);

        $cache->set('key1', 'value1');
        $this->assertEquals('value1', $cache->get('key1'));
        $this->assertTrue($cache->has('key1'));

        $cache->delete('key1');
        $this->assertFalse($cache->has('key1'));
        $this->assertNull($cache->get('key1'));
    }

    public function testArrayCacheWithDefaultValue(): void
    {
        $cache = new ArrayCache();

        $this->assertEquals('default', $cache->get('nonexistent', 'default'));
    }

    public function testArrayCacheIncrement(): void
    {
        $cache = new ArrayCache();

        $cache->set('counter', 10);
        $this->assertEquals(11, $cache->increment('counter'));
        $this->assertEquals(16, $cache->increment('counter', 5));
    }

    public function testArrayCacheDecrement(): void
    {
        $cache = new ArrayCache();

        $cache->set('counter', 10);
        $this->assertEquals(9, $cache->decrement('counter'));
        $this->assertEquals(4, $cache->decrement('counter', 5));
    }

    public function testArrayCacheRemember(): void
    {
        $cache = new ArrayCache();
        $callCount = 0;

        $result1 = $cache->remember('test', 300, function() use (&$callCount) {
            $callCount++;
            return 'computed';
        });

        $result2 = $cache->remember('test', 300, function() use (&$callCount) {
            $callCount++;
            return 'computed';
        });

        $this->assertEquals('computed', $result1);
        $this->assertEquals('computed', $result2);
        $this->assertEquals(1, $callCount);
    }

    public function testArrayCacheClear(): void
    {
        $cache = new ArrayCache(['prefix' => 'test:']);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $cache->clear();

        $this->assertFalse($cache->has('key1'));
        $this->assertFalse($cache->has('key2'));
    }

    public function testFileCacheBasicOperations(): void
    {
        $cache = new FileCache(['path' => $this->cachePath, 'prefix' => 'test:']);

        $cache->set('key1', 'value1', 60);
        $this->assertEquals('value1', $cache->get('key1'));
        $this->assertTrue($cache->has('key1'));

        $cache->delete('key1');
        $this->assertFalse($cache->has('key1'));
    }

    public function testFileCacheWithComplexData(): void
    {
        $cache = new FileCache(['path' => $this->cachePath]);

        $data = [
            'user' => ['id' => 1, 'name' => 'John'],
            'settings' => ['theme' => 'dark']
        ];

        $cache->set('complex', $data);
        $retrieved = $cache->get('complex');

        $this->assertEquals($data, $retrieved);
    }

    public function testFileCacheExpiration(): void
    {
        $cache = new FileCache(['path' => $this->cachePath]);

        $cache->set('expiring', 'value', 1);
        $this->assertEquals('value', $cache->get('expiring'));

        sleep(2);

        $this->assertNull($cache->get('expiring'));
    }

    public function testCacheManagerFactory(): void
    {
        $cache = Cache::make(['driver' => 'array', 'prefix' => 'app:']);

        $cache->set('test', 'value');
        $this->assertEquals('value', $cache->get('test'));
    }

    public function testCacheStaticFacade(): void
    {
        Cache::setInstance(Cache::make(['driver' => 'array']));

        Cache::set('facade_test', 'works');
        $this->assertEquals('works', Cache::get('facade_test'));
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
