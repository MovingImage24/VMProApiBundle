<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\EventListener;

use MovingImage\Bundle\VMProApiBundle\Decorator\BlackholeCacheItemPoolDecorator;
use MovingImage\Client\VMPro\ApiClient;
use MovingImage\Client\VMPro\ApiClient\AbstractApiClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This listener kicks in only if the `cache_bypass_argument` bundle config option is set.
 * If the request contains an argument matching the value configured in the aforementioned config option
 * and the value of that argument evaluates to true, this listener will modify the cache pool implementation
 * used by the VMPro API client, by decorating it with a blackhole cache implementation:
 * one that stores responses to cache, but never returns a hit.
 */
class BypassCacheListener implements EventSubscriberInterface
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var string|null
     */
    private $cacheBypassArgument;

    public function __construct(ApiClient $apiClient, ?string $cacheBypassArgument = null)
    {
        $this->apiClient = $apiClient;
        $this->cacheBypassArgument = $cacheBypassArgument;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (is_null($this->cacheBypassArgument)) {
            return;
        }

        $request = $event->getRequest();
        if ($request->get($this->cacheBypassArgument) || $request->cookies->get($this->cacheBypassArgument)) {
            /** @var AbstractApiClient $apiClient */
            $cachePool = new BlackholeCacheItemPoolDecorator($this->apiClient->getCacheItemPool());
            $this->apiClient->setCacheItemPool($cachePool);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
