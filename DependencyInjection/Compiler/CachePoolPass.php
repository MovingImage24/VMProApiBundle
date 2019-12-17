<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CachePoolPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        // inject cache pool to API client
        $clientDefinition = $container->getDefinition('vmpro_api.client');
        $clientDefinitionArguments = $clientDefinition->getArguments();

        $clientDefinitionArguments[2] = $this->getServiceReference($container, 'vm_pro_api_logger');
        $clientDefinitionArguments[3] = $this->getServiceReference($container, 'vm_pro_api_cache_pool');
        $clientDefinitionArguments[4] = $container->getParameter('vm_pro_api_cache_ttl');

        $clientDefinition->setArguments($clientDefinitionArguments);

        // inject cache pool to TokenManager
        $tokenManagerDefinition = $container->getDefinition('vmpro_api.token_manager');
        $tokenManagerDefinitionArguments = $tokenManagerDefinition->getArguments();
        $tokenManagerDefinitionArguments[3] = $this->getServiceReference($container, 'vm_pro_api_cache_pool');

        $tokenManagerDefinition->setArguments($tokenManagerDefinitionArguments);
    }

    /**
     * Returns a reference to a service, if that service exists in the container.
     * The service ID is obtained by fetching the value of the provided $parameterName.
     * This allows us to inject a service into the bundle by supplying the service ID in the bundle configuration.
     *
     * If the specified service does not exist in the container, an exception is thrown.
     *
     * @throws \Exception
     */
    private function getServiceReference(ContainerBuilder $container, string $parameterName): ?Reference
    {
        $serviceId = $container->getParameter($parameterName);
        if (empty($serviceId)) {
            return null;
        }

        if ($container->hasDefinition($serviceId) || $container->hasAlias($serviceId)) {
            return new Reference($serviceId);
        }

        throw new \Exception(sprintf('Service "%s" specified in parameter "%s" does not exist', $serviceId, $parameterName));
    }
}
