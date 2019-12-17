<?php

namespace MovingImage\Bundle\VMProApiBundle;

use MovingImage\Bundle\VMProApiBundle\DependencyInjection\Compiler\CachePoolPass;
use MovingImage\Bundle\VMProApiBundle\DependencyInjection\Compiler\StopwatchPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class VMProApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CachePoolPass());
        $container->addCompilerPass(new StopwatchPass());
    }
}
