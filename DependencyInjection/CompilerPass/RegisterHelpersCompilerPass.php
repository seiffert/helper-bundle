<?php

namespace Seiffert\HelperBundle\DependencyInjection\CompilerPass;

use Seiffert\HelperBundle\HelperInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterHelpersCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $tags = $container->findTaggedServiceIds('seiffert.helper');

        foreach ($tags as $id => $attributes) {
            $brokerIds = array();
            foreach ($attributes as $tagAttributes) {
                if (isset($tagAttributes['broker'])) {
                    $brokerIds[] = $tagAttributes['broker'];
                } else {
                    $brokerIds[] = 'seiffert.helper.broker';
                }
            }

            $this->addHelperMethodsOfServiceWithIdToBrokersWithIds($id, $brokerIds, $container);
        }
    }

    /**
     * @param string $id
     * @param array|string[] $brokerIds
     * @param ContainerBuilder $container
     */
    private function addHelperMethodsOfServiceWithIdToBrokersWithIds($id, array $brokerIds, ContainerBuilder $container)
    {
        foreach ($brokerIds as $brokerId) {
            if (!$container->hasDefinition($brokerId)) {
                $this->addBrokerDefinition($brokerId, $container);
            }

            $brokerDef = $container->getDefinition($brokerId);

            $this->addHelperMethodsOfServiceWithIdToBroker($id, $brokerDef, $container);
        }
    }

    /**
     * @param string $id
     * @param ContainerBuilder $container
     */
    private function addBrokerDefinition($id, ContainerBuilder $container)
    {
        $definition = new Definition();
        $definition->setClass('Seiffert\HelperBundle\HelperBroker');

        $container->setDefinition($id, $definition);
    }

    /**
     * @param string $id
     * @param Definition $broker
     * @param ContainerBuilder $container
     */
    private function addHelperMethodsOfServiceWithIdToBroker($id, Definition $broker, ContainerBuilder $container)
    {
        $helperMethods = $this->resolveHelperMethodsOfService($id, $container);

        foreach ($helperMethods as $helperMethod) {
            $broker->addMethodCall(
                'addHelper',
                array($helperMethod, array(new Reference($id), $helperMethod))
            );
        }
    }

    /**
     * @param string $id
     * @param ContainerBuilder $container
     * @return array|\string[]
     */
    private function resolveHelperMethodsOfService($id, ContainerBuilder $container)
    {
        $serviceDef = $container->getDefinition($id);
        $className = $this->resolveClassName($serviceDef->getClass(), $container);

        $helperMethods = array();
        if (is_a($className, 'Seiffert\HelperBundle\HelperInterface', true)) {
            $helperMethods = $this->getHelperMethodsOfHelper($id, $container);
        } else {
            $helperMethods = $this->getPublicMethodNamesOfClass($className);
        }

        return $helperMethods;
    }

    /**
     * @param string $id
     * @param ContainerBuilder $container@
     * @return array|string[]
     */
    private function getHelperMethodsOfHelper($id, ContainerBuilder $container)
    {
        /** @var HelperInterface $service */
        $service = $container->get($id);

        return $service->getHelperMethodNames();
    }

    /**
     * @param string $className
     * @return array|string[]
     */
    private function getPublicMethodNamesOfClass($className)
    {
        $reflection = new \ReflectionClass($className);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        return array_map(
            function (\ReflectionMethod $method) {
                return $method->getName();
            },
            $methods
        );
    }

    /**
     * @param string $class
     * @param ContainerBuilder $container
     * @return string
     */
    private function resolveClassName($class, ContainerBuilder $container)
    {
        if (0 === strpos($class, '%')) {
            $class = $container->getParameter(str_replace('%', '', $class));
        }

        return $class;
    }
}
