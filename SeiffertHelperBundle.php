<?php

namespace Seiffert\HelperBundle;

use Seiffert\HelperBundle\DependencyInjection\CompilerPass\RegisterHelpersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SeiffertHelperBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterHelpersCompilerPass());
    }
}
