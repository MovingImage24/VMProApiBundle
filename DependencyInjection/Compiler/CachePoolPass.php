<?php

namespace MovingImage\Bundle\VMProApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CachePoolPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $clientDefinition = $container->getDefinition('vmpro_api.client');

        $clientDefinition->setArgument(2, $this->getServiceReference($container, 'vm_pro_api_logger'));
        $clientDefinition->setArgument(3, $this->getServiceReference($container, 'vm_pro_api_cache_pool'));
        $clientDefinition->setArgument(4, $container->getParameter('vm_pro_api_cache_ttl'));
    }

    /**
     * Returns a reference to a service, if that service exists in the container.
     * The service ID is obtained by fetching the value of the provided $parameterName.
     * This allows us to inject a service into the bundle by supplying the service ID in the bundle configuration.
     *
     * If the specified service does not exist in the container, an exception is thrown.
     *
     * @param ContainerBuilder $container
     * @param string           $parameterName
     *
     * @return Reference|null
     *
     * @throws \Exception
     */
    private function getServiceReference(ContainerBuilder $container, $parameterName)
    {
        $serviceId = $container->getParameter($parameterName);
        if (empty($serviceId)) {
            return null;
        }

        if ($container->hasDefinition($serviceId) || $container->hasAlias($serviceId)) {
            return new Reference($serviceId);
        }

        throw new \Exception(sprintf(
            'Service "%s" specified in parameter "%s" does not exist',
            $serviceId,
            $parameterName
        ));
    }
}
