<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests\DependencyInjection\VMProApiExtensionTest;

use MovingImage\Bundle\VMProApiBundle\DependencyInjection\VMProApiExtension;
use MovingImage\Bundle\VMProApiBundle\Tests\DependencyInjection\AbstractTestCase;
use MovingImage\Client\VMPro\ApiClient\Guzzle6ApiClient;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Guzzle6Test extends AbstractTestCase
{
    /**
     * Assert whether when Guzzle ^6.0 is installed, the right instance
     * of the API client is placed in the dependency injection container.
     */
    public function testHasGuzzle6Client(): void
    {
        $container = new ContainerBuilder();
        $loader = new VMProApiExtension();
        $config = $this->getFullConfig();

        $loader->load($config, $container);

        $this->assertInstanceOf(Guzzle6ApiClient::class, $container->get('vmpro_api.client'));
    }
}
