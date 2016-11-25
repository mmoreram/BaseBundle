<?php

/*
 * This file is part of the BaseBundle for Symfony2.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\BaseBundle\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TagCompilerPass.
 */
abstract class TagCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->getCollectorServiceName())) {
            return;
        }

        $definition = $container->findDefinition(
            $this->getCollectorServiceName()
        );

        $taggedServices = $container->findTaggedServiceIds(
            $this->getTagName()
        );

        /*
         * Get services with certain tag and add it into a services array
         */
        $services = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $services[$id] = $attributes;
            }
        }

        /*
         * If these services must be sorted, then sort them
         */
        if ($this->sortByPriority()) {
            uasort($services, function ($a, $b) {
                $priorityA = $a['priority'] ?? 0;
                $priorityB = $b['priority'] ?? 0;

                return $priorityA <=> $priorityB;
            });
        }

        /*
         * Per each service, add a new method call reference
         */
        foreach ($services as $serviceId => $serviceAttributes) {
            $definition->addMethodCall(
                $this->getCollectorMethodName(),
                [new Reference($serviceId)]
            );
        }
    }

    /**
     * Get collector service name.
     *
     * @return string Collector service name
     */
    abstract public function getCollectorServiceName() : string;

    /**
     * Get collector method name.
     *
     * @return string Collector method name
     */
    abstract public function getCollectorMethodName() : string;

    /**
     * Get tag name.
     *
     * @return string Tag name
     */
    abstract public function getTagName() : string;

    /**
     * Sort by priority.
     *
     * @return bool
     */
    public function sortByPriority() : bool
    {
        return false;
    }
}
