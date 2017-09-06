<?php

namespace MovingImage\Bundle\VMProApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $clientDefinition = $container->getDefinition('vmpro_api.client');

        $logger = $this->getServiceReference($container, $container->getParameter('vm_pro_api_logger'));
        $cachePool = $this->getServiceReference($container, $container->getParameter('vm_pro_api_cache_pool'));
        $cacheTtl = $container->getParameter('vm_pro_api_cache_ttl');

        $clientDefinition->setArgument(2, $logger);
        $clientDefinition->setArgument(3, $cachePool);
        $clientDefinition->setArgument(4, $cacheTtl);
    }

    /**
     * Returns a reference to a service, if that service exists in the container.
     * Otherwise returns null.
     *
     * @param ContainerBuilder $container
     * @param string           $serviceId
     *
     * @return Reference|null
     */
    private function getServiceReference(ContainerBuilder $container, $serviceId)
    {
        if ($serviceId && ($container->hasDefinition($serviceId) || $container->hasAlias($serviceId))) {
            return new Reference($serviceId);
        }
    }
}
