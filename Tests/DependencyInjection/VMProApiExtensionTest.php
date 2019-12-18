<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\Tests\DependencyInjection;

use MovingImage\Bundle\VMProApiBundle\DependencyInjection\VMProApiExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VMProApiExtensionTest extends AbstractTestCase
{
    /**
     * Assert whether an exception is thrown when required configuration
     * key 'vm_pro_api.credentials' is missing.
     */
    public function testConfigurationLoadThrowsExceptionUnlessCredentials(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $loader = new VMProApiExtension();
        $config = $this->getEmptyConfig();

        unset($config['vm_pro_api']['credentials']);
        $loader->load($config, new ContainerBuilder());
    }

    /**
     * Assert whether an exception is thrown when required configuration
     * key 'vm_pro_api.credentials.username' is missing.
     */
    public function testConfigurationLoadThrowsExceptionUnlessUsername(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $loader = new VMProApiExtension();
        $config = $this->getEmptyConfig();

        unset($config['vm_pro_api']['credentials']['username']);
        $loader->load($config, new ContainerBuilder());
    }

    /**
     * Assert whether an exception is thrown when required configuration
     * key 'vm_pro_api.credentials.password' is missing.
     */
    public function testConfigurationLoadThrowsExceptionUnlessPassword(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $loader = new VMProApiExtension();
        $config = $this->getEmptyConfig();

        unset($config['vm_pro_api']['credentials']['password']);
        $loader->load($config, new ContainerBuilder());
    }

    /**
     * Assert whether the configuration keys and values are successfully
     * written as parameters.
     */
    public function testParametersForFullConfig(): void
    {
        $container = new ContainerBuilder();
        $loader = new VMProApiExtension();
        $config = $this->getFullConfig();

        $loader->load($config, $container);

        $this->assertEquals('http://google.com/', $container->getParameter('vm_pro_api_base_url'));
        $this->assertEquals(5, $container->getParameter('vm_pro_api_default_vm_id'));
        $this->assertEquals('test@test.com', $container->getParameter('vm_pro_api_credentials_username'));
        $this->assertEquals('test_password', $container->getParameter('vm_pro_api_credentials_password'));
    }

    /**
     * Assert whether the configuration keys and values are successfully
     * written as parameters with the right default values.
     */
    public function testDefaultParameters(): void
    {
        $container = new ContainerBuilder();
        $loader = new VMProApiExtension();
        $config = $this->getEmptyConfig();

        $loader->load($config, $container);

        $this->assertEquals('https://api.video-cdn.net/v1/vms/', $container->getParameter('vm_pro_api_base_url'));
        $this->assertEquals(0, $container->getParameter('vm_pro_api_default_vm_id'));
    }
}
