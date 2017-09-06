<?php

namespace MovingImage\Bundle\VMProApiBundle;

use MovingImage\Bundle\VMProApiBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class VMProApiBundle.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class VMProApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CompilerPass());
    }
}
