<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Decorator;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Decorator that wraps around any CacheItemPoolInterface implementation
 * and overrides the `isHit` method by always returning false.
 * Therefore, this is a CacheItemPool implementation useful for forcing cache to be refreshed.
 */
class BlackholeCacheItemPoolDecorator implements CacheItemPoolInterface
{
    /**
     * Decorated CacheItemPool implementation.
     *
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key): CacheItemInterface
    {
        return new BlackholeCacheItemDecorator($this->cacheItemPool->getItem($key));
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $items = [];
        foreach ($this->cacheItemPool->getItems($keys) as $item) {
            $items[] = new BlackholeCacheItemDecorator($item);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key): bool
    {
        return $this->cacheItemPool->hasItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        return $this->cacheItemPool->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key): bool
    {
        return $this->cacheItemPool->deleteItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys): bool
    {
        return $this->cacheItemPool->deleteItems($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item): bool
    {
        if ($item instanceof BlackholeCacheItemDecorator) {
            $item = $item->getDecoratedItem();
        }

        return $this->cacheItemPool->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        if ($item instanceof BlackholeCacheItemDecorator) {
            $item = $item->getDecoratedItem();
        }

        return $this->cacheItemPool->saveDeferred($item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): bool
    {
        return $this->cacheItemPool->commit();
    }
}
