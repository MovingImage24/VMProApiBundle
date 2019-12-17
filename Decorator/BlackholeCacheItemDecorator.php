<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Decorator;

use Psr\Cache\CacheItemInterface;

/**
 * Decorator that wraps around any CacheItemInterface implementation
 * and overrides the `isHit` method by always returning false.
 * Therefore, this is a CacheItem implementation useful for forcing cache to be refreshed.
 */
class BlackholeCacheItemDecorator implements CacheItemInterface
{
    /**
     * Decorated CacheItem implementation.
     *
     * @var CacheItemInterface
     */
    private $cacheItem;

    public function __construct(CacheItemInterface $cacheItem)
    {
        $this->cacheItem = $cacheItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return $this->cacheItem->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->cacheItem->get();
    }

    /**
     * {@inheritdoc}
     */
    public function isHit(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value): CacheItemInterface
    {
        return $this->cacheItem->set($value);
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration): CacheItemInterface
    {
        return $this->cacheItem->expiresAt($expiration);
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time): CacheItemInterface
    {
        return $this->cacheItem->expiresAfter($time);
    }

    /**
     * Returns the decorated CacheItemInterface implementation.
     */
    public function getDecoratedItem(): CacheItemInterface
    {
        return $this->cacheItem;
    }
}
