<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests\EventListener;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use MovingImage\Bundle\VMProApiBundle\EventListener\BypassCacheListener;
use MovingImage\Client\VMPro\ApiClient;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BypassCacheListenerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * Tests onKernelRequest method.
     *
     * @dataProvider dataProvider
     *
     * @param string $bypassCacheArgument
     * @param array  $query
     * @param array  $request
     * @param array  $attributes
     * @param array  $cookies
     * @param bool   $isHit
     */
    public function testOnKernelRequest(
        $bypassCacheArgument,
        array $query,
        array $request,
        array $attributes,
        array $cookies,
        $isHit
    ) {
        $apiClient = $this->getApiClient();
        $listener = new BypassCacheListener($apiClient, $bypassCacheArgument);
        $request = new Request($query, $request, $attributes, $cookies);
        $kernel = $this->createMock(HttpKernel::class);

        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertIsHit($isHit, $apiClient->getCacheItemPool());
    }

    /**
     * Creates an ApiClient instance with mocked dependencies
     * and ArrayAdapter as cache pool.
     *
     * @return ApiClient
     */
    private function getApiClient()
    {
        $client = $this->createMock(ClientInterface::class);

        return new ApiClient($client, $this->serializer, new ArrayAdapter());
    }

    /**
     * Asserts that storing an item to the provided pool followed by immediately
     * fetching it again from the pool will result in the specified "hit" status.
     * ($isHit = true -> expecting a cache hit; $isHit = false -> expecting a cache miss).
     *
     * @param $isHit
     * @param CacheItemPoolInterface $pool
     */
    private function assertIsHit($isHit, CacheItemPoolInterface $pool)
    {
        $item = $pool->getItem('test');
        $item->set('test');
        $pool->save($item);
        $this->assertSame($isHit, $pool->getItem('test')->isHit());
    }

    /**
     * Data provider for testOnKernelRequest.
     * Provides various combinations of request arguments and configuration,
     * as well as the expected behavior.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            [null, [], [], [], [], true],
            [null, ['bypass_cache' => 1], [], [], [], true],
            [null, [], ['bypass_cache' => 1], [], [], true],
            [null, [], [], ['bypass_cache' => 1], [], true],
            [null, [], [], [], ['bypass_cache' => 1], true],

            ['bypass_cache', ['bypass_cache' => 1], [], [], [], false],
            ['bypass_cache', [], ['bypass_cache' => 1], [], [], false],
            ['bypass_cache', [], [], ['bypass_cache' => 1], [], false],
            ['bypass_cache', [], [], [], ['bypass_cache' => 1], false],
        ];
    }
}
