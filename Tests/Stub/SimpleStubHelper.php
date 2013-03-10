<?php

namespace Seiffert\HelperBundle\Tests\Stub;

use Seiffert\HelperBundle\HelperInterface;

class SimpleStubHelper implements HelperInterface
{
    public function simpleHelperMethod()
    {
        return true;
    }

    /**
     * @return string[]|array
     */
    public static function getHelperMethodNames()
    {
        return array('simpleHelperMethod');
    }
}
