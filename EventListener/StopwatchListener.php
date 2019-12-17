<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\EventListener;

use MovingImage\Bundle\VMProApiBundle\Service\Stopwatch;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets the headers with durations of each API request, as well as the total time for all requests.
 */
class StopwatchListener implements EventSubscriberInterface
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param bool $enabled (if false, this listener will be effectively bypassed)
     */
    public function __construct(Stopwatch $stopwatch, bool $enabled)
    {
        $this->stopwatch = $stopwatch;
        $this->enabled = $enabled;
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $totalDuration = 0;

        foreach ($this->stopwatch->getEvents() as $name => $stopwatchEvent) {
            $duration = $stopwatchEvent->getDuration();
            $totalDuration += $duration;
            $this->setHeader($event->getResponse(), $name, $duration);
        }

        $this->setHeader($event->getResponse(), 'total', $totalDuration);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    /**
     * Sets the header for the specified stage (eg X-API-RESPONSE-GET-VIDEOS).
     */
    private function setHeader(Response $response, string $stage, int $duration): void
    {
        // disallow non-alphanumeric characters in the header
        $stage = preg_replace('/[^\w]/', '', $stage);
        $headerName = 'X-API-RESPONSE-'.strtoupper($stage);
        $response->headers->set($headerName, $duration);
    }
}
