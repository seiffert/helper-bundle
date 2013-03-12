<?php


namespace Seiffert\HelperBundle;

use Seiffert\HelperBundle\Exception\InvalidHelperException;
use Seiffert\HelperBundle\Exception\UnknownHelperException;

class HelperBroker
{
    /**
     * @var array
     */
    private $helpers = array();

    public function __construct(HelperSet $helperSet = null)
    {
        if (null !== $helperSet) {
            $helperSet->registerHelpers($this);
        }
    }

    /**
     * @param string $name
     * @param callable $helper
     * @throws InvalidHelperException
     */
    public function addHelper($name, $helper)
    {
        if (!is_callable($helper)) {
            throw new InvalidHelperException('The provided helper is not callable.');
        }

        $this->helpers[$name] = $helper;
    }

    /**
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws UnknownHelperException
     */
    public function __call($methodName, $arguments)
    {
        if (!isset($this->helpers[$methodName])) {
            throw new UnknownHelperException('Could not find helper object with method ' . $methodName);
        }

        return call_user_func_array($this->helpers[$methodName], $arguments);
    }
}
