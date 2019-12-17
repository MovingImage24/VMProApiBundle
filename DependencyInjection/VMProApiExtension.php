<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\HttpKernel\Kernel;

class VMProApiExtension extends ConfigurableExtension
{
    /**
     * Flatten multi-dimensional Symfony configuration array into a
     * one-dimensional parameters array.
     */
    private function flattenParametersFromConfig(ContainerBuilder $container, array $configs, string $root): array
    {
        $parameters = [];

        foreach ($configs as $key => $value) {
            $parameterKey = sprintf('%s_%s', $root, $key);
            if (is_array($value)) {
                $parameters = array_merge(
                    $parameters,
                    $this->flattenParametersFromConfig($container, $value, $parameterKey)
                );

                continue;
            }

            $parameters[$parameterKey] = $value;
        }

        return $parameters;
    }

    /**
     * Load the appropriate service container configurations based on which
     * GuzzleHttp library is present in the project.
     *
     * @throws \Exception
     */
    protected function loadInternal(array $configs, ContainerBuilder $container): void
    {
        $parameters = $this->flattenParametersFromConfig($container, $configs, 'vm_pro_api');
        foreach ($parameters as $key => $value) {
            $container->setParameter($key, $value);
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        // Always load the services that will always be the same
        $loader->load('services/main.yml');

        // only load ratings service if the meta data fields are configured
        if (array_key_exists('vm_pro_api_rating_meta_data_fields_average', $parameters)
            && array_key_exists('vm_pro_api_rating_meta_data_fields_count', $parameters)) {
            $loader->load('services/ratings.yml');
        }

        // Dynamically load service configurations that are specific
        // to which Guzzle version is installed
        $loader->load('services/guzzle6.yml');

        if (version_compare(Kernel::VERSION, '3.3', '>=')) {
            $loader->load('services/symfony3.yml');
        }
    }
}
