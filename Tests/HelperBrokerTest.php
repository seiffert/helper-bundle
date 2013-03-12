<?php

namespace Seiffert\HelperBundle\Tests;

use Seiffert\HelperBundle\HelperBroker;
use Seiffert\HelperBundle\Tests\Stub\SimpleStubHelper;

/**
 * @covers Seiffert\HelperBundle\HelperBroker
 */
class HelperBrokerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HelperBroker
     */
    private $broker;

    public function setUp()
    {
        $this->broker = new HelperBroker();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('Seiffert\HelperBundle\HelperBroker', $this->broker);
    }

    public function testAddHelper()
    {
        $this->broker->addHelper('myName', array(new SimpleStubHelper(), 'simpleHelperMethod'));

        $this->assertTrue($this->broker->myName());
    }

    public function testAddInvalidHelper()
    {
        $this->setExpectedException('Seiffert\HelperBundle\Exception\InvalidHelperException');
        $this->broker->addHelper('myName', 'invalidCallable');
    }

    public function testUnknownHelperMethodThrowsException()
    {
        $this->setExpectedException('Seiffert\HelperBundle\Exception\UnknownHelperException');
        $this->broker->unknownMethod();
    }

    public function testHelperBrokerRegistersHelperSet()
    {
        $helperSet = $this->getMock('Seiffert\HelperBundle\HelperSet');
        $helperSet->expects($this->once())
            ->method('registerHelpers')
            ->with($this->isInstanceOf('Seiffert\HelperBundle\HelperBroker'));

        new HelperBroker($helperSet);
    }
}
