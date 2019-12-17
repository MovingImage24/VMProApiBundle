<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests\Decorator;

use MovingImage\Bundle\VMProApiBundle\Decorator\BlackholeCacheItemDecorator;
use MovingImage\Bundle\VMProApiBundle\Decorator\BlackholeCacheItemPoolDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class BlackholeCacheItemPoolDecoratorTest extends TestCase
{
    /**
     * Asserts that blackhole pool will always report a cache miss.
     */
    public function testIsHit()
    {
        $cachePool = $this->getCachePool(['test' => 'test']);

        $blackholePool = new BlackholeCacheItemPoolDecorator($cachePool);

        $this->assertTrue($cachePool->getItem('test')->isHit());
        $this->assertFalse($blackholePool->getItem('test')->isHit());
    }

    /**
     * Asserts that item returned by the blackhole pool is the correct type.
     */
    public function testGetItem()
    {
        $cachePool = $this->getCachePool(['test' => 'test']);
        $blackholePool = new BlackholeCacheItemPoolDecorator($cachePool);

        $this->assertInstanceOf(BlackholeCacheItemDecorator::class, $blackholePool->getItem('test'));
    }

    /**
     * Asserts that items returned by the blackhole pool are the correct type.
     */
    public function testGetItems()
    {
        $cachePool = $this->getCachePool(['test1' => 'test1', 'test2' => 'test2']);
        $blackholePool = new BlackholeCacheItemPoolDecorator($cachePool);

        foreach ($blackholePool->getItems(['test1', 'test2']) as $item) {
            $this->assertInstanceOf(BlackholeCacheItemDecorator::class, $item);
        }
    }

    /**
     * Asserts that decorated item returned by the blackhole pool is of the correct type.
     */
    public function testGetDecoratedItem()
    {
        $cachePool = $this->getCachePool(['test' => 'test']);
        $cacheItem = $cachePool->getItem('test');
        $blackholePool = new BlackholeCacheItemPoolDecorator($cachePool);
        $blackholeItem = $blackholePool->getItem('test');

        $this->assertInstanceOf(get_class($cacheItem), $blackholeItem->getDecoratedItem());
    }

    /**
     * Returns an implementation of the CacheItemPoolInterface
     * initialized with the values provided in the array.
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCachePool(array $values): CacheItemPoolInterface
    {
        $cachePool = new ArrayAdapter();
        foreach ($values as $key => $value) {
            $cacheItem = $cachePool->getItem($key);
            $cacheItem->set($value);
            $cachePool->save($cacheItem);
        }

        return $cachePool;
    }
}
