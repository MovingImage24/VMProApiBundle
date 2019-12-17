<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Service;

use MovingImage\Client\VMPro\Interfaces\StopwatchInterface;
use Symfony\Component\Stopwatch\Stopwatch as SymfonyStopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * Adapter for Symfony Stopwatch, implementing the StopwatchInterface.
 */
class Stopwatch implements StopwatchInterface
{
    /**
     * @var SymfonyStopwatch
     */
    private $delegate;

    /**
     * @var StopwatchEvent[]
     */
    private $events = [];

    public function __construct(SymfonyStopwatch $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function start(string $name, ?string $category = null): void
    {
        $this->delegate->start($name, $category);
    }

    /**
     * {@inheritdoc}
     */
    public function stop(string $name): void
    {
        $this->events[$name] = $this->delegate->stop($name);
    }

    /**
     * @return StopwatchEvent[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
