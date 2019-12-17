<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests;

use MovingImage\Bundle\VMProApiBundle\Command\TestConnectionCommand;
use MovingImage\Client\VMPro\Entity\Channel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MovingImage\Client\VMPro\ApiClient;

class TestConnectionCommandTest extends TestCase
{
    private function createCommandTester(ContainerInterface $container, Application $application = null)
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);
        $command = new TestConnectionCommand();
        $command->setContainer($container);
        $application->add($command);

        return new CommandTester($application->find('vmpro-api:test-connection'));
    }

    private function getContainer($success = true)
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $client = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        if (true === $success) {
            $channel = (new Channel())
                ->setName('Test');

            $client
                ->expects($this->once())
                ->method('getChannels')
                ->with(5)
                ->willReturn($channel);
        } else {
            $client
                ->expects($this->once())
                ->method('getChannels')
                ->with(5)
                ->will($this->throwException(new \Exception()));
        }

        $container
            ->expects($this->once())
            ->method('get')
            ->with('vmpro_api.client')
            ->willReturn($client);

        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('vm_pro_api_default_vm_id')
            ->willReturn(5);

        return $container;
    }

    public function testSuccess()
    {
        $container = $this->getContainer(true);
        $commandTester = $this->createCommandTester($container);

        $this->assertEquals(0, $commandTester->execute([]));
    }

    public function testFail()
    {
        $container = $this->getContainer(false);
        $commandTester = $this->createCommandTester($container);

        $this->assertEquals(1, $commandTester->execute([]));
    }
}
