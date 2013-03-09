<?php

namespace Seiffert\HelperBundle\Tests\DependencyInjection;

use Seiffert\HelperBundle\DependencyInjection\SeiffertHelperBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers Seiffert\HelperBundle\DependencyInjection\SeiffertHelperBundleExtension
 */
class SeiffertHelperBundleExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SeiffertHelperBundleExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new SeiffertHelperBundleExtension();
    }

    public function testInstantiation()
    {
        $this->assertinstanceOf(
            'Seiffert\HelperBundle\DependencyInjection\SeiffertHelperBundleExtension',
            $this->extension
        );

        $this->assertInstanceOf('Symfony\Component\HttpKernel\DependencyInjection\Extension', $this->extension);
    }

    public function testExtensionProvidesHelperBrokerService()
    {
        $container = new ContainerBuilder();

        $this->extension->load(array(), $container);

        $this->assertTrue($container->has('seiffert.helper.broker'));
    }
}
