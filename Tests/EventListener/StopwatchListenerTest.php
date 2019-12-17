<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests\EventListener;

use MovingImage\Bundle\VMProApiBundle\EventListener\StopwatchListener;
use MovingImage\Bundle\VMProApiBundle\Service\Stopwatch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Stopwatch\Stopwatch as SymfonyStopwatch;

class StopwatchListenerTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestOnKernelResponse
     */
    public function testOnKernelResponse(bool $enabled): void
    {
        $stopwatch = new Stopwatch(new SymfonyStopwatch());
        $stopwatch->start('test');
        $stopwatch->stop('test');
        $listener = new StopwatchListener($stopwatch, $enabled);
        $request = $this->createMock(Request::class);
        $response = Response::create();
        $kernel = $this->createMock(HttpKernel::class);
        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $listener->onKernelResponse($event);

        if ($enabled) {
            $this->assertTrue($response->headers->has('X-API-RESPONSE-TEST'));
        } else {
            $this->assertFalse($response->headers->has('X-API-RESPONSE-TEST'));
        }
    }

    /**
     * Data provider for testOnKernelResponse.
     */
    public function dataProviderForTestOnKernelResponse(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
