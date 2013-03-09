<?php

namespace Seiffert\HelperBundle;

use Seiffert\HelperBundle\SeiffertHelperBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers Seiffert\HelperBundle\SeiffertHelperBundle
 */
class SeiffertHelperBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SeiffertHelperBundle
     */
    private $bundle;

    public function setUp()
    {
        $this->bundle = new SeiffertHelperBundle();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Bundle\Bundle', $this->bundle);
        $this->assertInstanceOf('Seiffert\HelperBundle\SeiffertHelperBUndle', $this->bundle);
    }

    public function testBuildAddsCompilerPass()
    {
        $containerBuilder = $this->getMock(
            'Symfony\Component\DependencyInjection\ContainerBuilder',
            array(),
            array(),
            '',
            false
        );

        $containerBuilder->expects($this->once())
            ->method('addCompilerPass')
            ->with(
                $this->isInstanceOf(
                    'Seiffert\HelperBundle\DependencyInjection\CompilerPass\RegisterHelpersCompilerPass'
                )
            )
            ->will($this->returnSelf());

        $this->bundle->build($containerBuilder);
    }
}
