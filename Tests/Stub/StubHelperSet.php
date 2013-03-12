<?php

namespace Seiffert\HelperBundle\Tests\Stub;

use Seiffert\HelperBundle\HelperSet;

class StubHelperSet extends HelperSet
{
    /**
     * @var array
     */
    private $helpers;

    /**
     * @param array $helpers
     */
    public function __construct(array $helpers = array())
    {
        $this->helpers = $helpers;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return $this->helpers;
    }
}
