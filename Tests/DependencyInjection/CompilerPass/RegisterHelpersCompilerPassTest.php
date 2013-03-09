<?php

namespace Seiffert\HelperBundle\Tests\DependencyInjection;

use Seiffert\HelperBundle\DependencyInjection\CompilerPass\RegisterHelpersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers Seiffert\HelperBundle\DependencyInjection\CompilerPass\RegisterHelpersCompilerPass
 */
class RegisterHelpersCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterHelpersCompilerPass
     */
    private $compilerPass;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var Definition
     */
    private $helperBrokerDefinition;

    public function setUp()
    {
        $this->compilerPass = new RegisterHelpersCompilerPass();
    }

    public function tearDown()
    {
        $this->containerBuilder = null;
        $this->helperBrokerDefinition = null;
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface',
            $this->compilerPass
        );
        $this->assertInstanceOf(
            'Seiffert\HelperBundle\DependencyInjection\CompilerPass\RegisterHelpersCompilerPass',
            $this->compilerPass
        );
    }

    public function testProcessRegistersExplicitHelperAtMainBroker()
    {
        $container = $this->getContainerBuilder();

        $mainBrokerDef = new Definition();
        $container->setDefinition('seiffert.helper.broker', $mainBrokerDef);

        $helperDef = new Definition();
        $helperDef->setClass('%test.helper.class%');
        $helperDef->addTag('seiffert.helper');
        $container->setDefinition('test.helper', $helperDef);
        $container->setParameter('test.helper.class', 'Seiffert\HelperBundle\Tests\Stub\SimpleStubHelper');

        $this->compilerPass->process($container);

        $calls = $mainBrokerDef->getMethodCalls();
        $this->assertCount(1, $calls);

        $this->assertEquals(
            $calls[0],
            array(
                'addHelper',
                array('simpleHelperMethod', array(new Reference('test.helper'), 'simpleHelperMethod'))
            )
        );
    }

    public function testProcessRegistersExplicitHelperAtCustomBroker()
    {
        $container = $this->getContainerBuilder();

        $helperDef = new Definition();
        $helperDef->setClass('Seiffert\HelperBundle\Tests\Stub\SimpleStubHelper');
        $helperDef->addTag('seiffert.helper', array('broker' => 'my.broker'));
        $container->setDefinition('test.helper', $helperDef);

        $this->compilerPass->process($container);

        $brokerDef = $container->getDefinition('my.broker');
        $calls = $brokerDef->getMethodCalls();
        $this->assertCount(1, $calls);

        $this->assertEquals(
            $calls[0],
            array(
                'addHelper',
                array('simpleHelperMethod', array(new Reference('test.helper'), 'simpleHelperMethod'))
            )
        );
    }

    public function testProcessRegistersExplicitHelperAtMainAndCustomBroker()
    {
        $container = $this->getContainerBuilder();

        $helperDef = new Definition();
        $helperDef->setClass('Seiffert\HelperBundle\Tests\Stub\SimpleStubHelper');
        $helperDef->addTag('seiffert.helper', array('broker' => 'my.broker'));
        $helperDef->addTag('seiffert.helper');
        $container->setDefinition('test.helper', $helperDef);

        $this->compilerPass->process($container);

        $brokerDef = $container->getDefinition('my.broker');
        $calls = $brokerDef->getMethodCalls();
        $this->assertCount(1, $calls);

        $this->assertEquals(
            $calls[0],
            array(
                'addHelper',
                array('simpleHelperMethod', array(new Reference('test.helper'), 'simpleHelperMethod'))
            )
        );

        $brokerDef = $container->getDefinition('seiffert.helper.broker');
        $calls = $brokerDef->getMethodCalls();
        $this->assertCount(1, $calls);

        $this->assertEquals(
            $calls[0],
            array(
                'addHelper',
                array('simpleHelperMethod', array(new Reference('test.helper'), 'simpleHelperMethod'))
            )
        );
    }

    public function testProcessRegistersImplicitHelperAtMainBroker()
    {
        $container = $this->getContainerBuilder();

        $mainBrokerDef = new Definition();
        $container->setDefinition('seiffert.helper.broker', $mainBrokerDef);

        $helperDef = new Definition();
        $helperDef->setClass('Seiffert\HelperBundle\Tests\Stub\NonHelperHelper');
        $helperDef->addTag('seiffert.helper');
        $container->setDefinition('test.helper', $helperDef);

        $this->compilerPass->process($container);

        $calls = $mainBrokerDef->getMethodCalls();
        $this->assertCount(2, $calls);

        $this->assertEquals(
            $calls[0],
            array(
                'addHelper',
                array('helpTrue', array(new Reference('test.helper'), 'helpTrue'))
            )
        );

        $this->assertEquals(
            $calls[1],
            array(
                'addHelper',
                array('helpFalse', array(new Reference('test.helper'), 'helpFalse'))
            )
        );
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainerBuilder()
    {
        if (null === $this->containerBuilder) {
            $this->containerBuilder = new ContainerBuilder();
        }

        return $this->containerBuilder;
    }

    /**
     * @return Definition
     */
    private function getHelperBrokerDefinition()
    {
        if (null === $this->helperBrokerDefinition) {
            $this->helperBrokerDefinition = new Definition();
        }

        return $this->helperBrokerDefinition;
    }
}
