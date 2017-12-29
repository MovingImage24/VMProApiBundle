<?php

namespace MovingImage\Bundle\VMProApiBundle\Tests;

use MovingImage\Bundle\VMProApiBundle\Command\TestConnectionCommand;
use MovingImage\Client\VMPro\Entity\Channel;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestConnectionCommandTest extends \PHPUnit_Framework_TestCase
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
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $client = $this->getMockBuilder('MovingImage\Client\VMPro\ApiClient')
            ->disableOriginalConstructor()
            ->getMock();

        if (true === $success) {
            $client
                ->expects($this->once())
                ->method('getChannels')
                ->with(5)
                ->will($this->returnValue(new Channel()));
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
            ->will($this->returnValue($client));

        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('vm_pro_api_default_vm_id')
            ->will($this->returnValue(5));

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
