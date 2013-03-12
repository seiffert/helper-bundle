<?php

namespace Seiffert\HelperBundleTests;

use Seiffert\HelperBundle\HelperBroker;
use Seiffert\HelperBundle\HelperSet;
use Seiffert\HelperBundle\Tests\Stub\SimpleStubHelper;
use Seiffert\HelperBundle\Tests\Stub\StubHelperSet;

/**
 * @covers Seiffert\HelperBundle\HelperSet
 */
class HelperSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HelperBroker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockHelperBroker;

    public function testRegisterHelpers()
    {
        $helper = new SimpleStubHelper();
        $helperSet = new StubHelperSet(array($helper));

        $this->getMockHelperBroker()
            ->expects($this->at(0))
            ->method('addHelper')
            ->with('simpleHelperMethod', array($helper, 'simpleHelperMethod'));

        $helperSet->registerHelpers($this->getMockHelperBroker());
    }

    /**
     * @return HelperBroker|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockHelperBroker()
    {
        if (null === $this->mockHelperBroker) {
            $this->mockHelperBroker = $this->getMock('Seiffert\HelperBundle\HelperBroker', array('addHelper'));
        }

        return $this->mockHelperBroker;
    }
}
