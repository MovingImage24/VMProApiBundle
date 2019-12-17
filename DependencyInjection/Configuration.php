<?php

declare(strict_types=1);

namespace MovingImage\Bundle\VMProApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @const Which API base URL to use by default.
     */
    private const DEFAULT_API_BASE_URL = 'https://api.video-cdn.net/v1/vms/';

    /**
     * @const Default OAuth URL (used for fetching and refreshing access token)
     */
    private const DEFAULT_OAUTH_URL = 'https://login.movingimage.com/auth/realms/platform/protocol/openid-connect/token';

    /**
     * Build the configuration structure for this bundle.
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vm_pro_api');

        $rootNode
            ->children()
                ->scalarNode('base_url')
                    ->defaultValue(self::DEFAULT_API_BASE_URL)
                ->end()
                ->scalarNode('oauth_url')
                    ->defaultValue(self::DEFAULT_OAUTH_URL)
                ->end()
                ->scalarNode('default_vm_id')
                    ->defaultValue(0)
                ->end()
                ->arrayNode('credentials')
                    ->isRequired()
                    ->children()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('rating_meta_data_fields')
                    ->children()
                        ->scalarNode('average')->isRequired()->end()
                        ->scalarNode('count')->isRequired()->end()
                    ->end()
                ->end()
                ->scalarNode('logger')
                    ->defaultValue(null)
                ->end()
                ->scalarNode('cache_pool')
                    ->defaultValue(null)
                ->end()
                ->scalarNode('cache_ttl')
                    ->defaultValue(null)
                ->end()
                ->scalarNode('cache_bypass_argument')
                    ->defaultValue(null)
                ->end()
                ->booleanNode('enable_stopwatch')
                    ->defaultValue(false)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
