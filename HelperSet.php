<?php

namespace Seiffert\HelperBundle;

abstract class HelperSet
{
    /**
     * @return array|object[]
     */
    abstract public function getHelpers();

    /**
     * @param HelperBroker $broker
     */
    public function registerHelpers(HelperBroker $broker)
    {
        foreach ($this->getHelpers() as $helper) {
            $this->registerHelper($helper, $broker);
        }
    }

    /**
     * @param object $helper
     * @param HelperBroker $broker
     */
    protected function registerHelper($helper, HelperBroker $broker)
    {
        if ($helper instanceof HelperInterface) {
            foreach ($helper->getHelperMethodNames() as $method) {
                $broker->addHelper($method, array($helper, $method));
            }
        } else {
            $this->registerPublicMethodsOfObjectAsHelperMethods($helper, $broker);
        }
    }

    /**
     * @param object $helper
     * @param HelperBroker $broker
     */
    protected function registerPublicMethodsOfObjectAsHelperMethods($helper, HelperBroker $broker)
    {
        $reflection = new \ReflectionClass($helper);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $broker->addHelper($method, array($helper, $method));
        }
    }
}
