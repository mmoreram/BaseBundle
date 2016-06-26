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

namespace Mmoreram\BaseBundle\CompilerPass\Abstracts;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass as OriginalAbstractMappingCompilerPass;

/**
 * Class AbstractMappingCompilerPass.
 */
abstract class AbstractMappingCompilerPass extends OriginalAbstractMappingCompilerPass
{
    /**
     * Add entity mapping given the entity name, given that all entity
     * definitions are built the same way and given as well that the method
     * addEntityMapping exists and is accessible.
     *
     * @param ContainerBuilder     $container
     * @param MappingBagCollection $mappingBags
     *
     * @return $this Self object
     */
    protected function addEntityMappings(
        ContainerBuilder $container,
        MappingBagCollection $mappingBags
    ) {
        foreach ($mappingBags->all() as $mappingBag) {
            $this
                ->addEntityMapping(
                    $container,
                    $mappingBag->getManager(),
                    $mappingBag->getClass(),
                    $mappingBag->getMappingFile(),
                    $mappingBag->isEnabled()
                );
            $this
                ->addEntityManager(
                    $container,
                    $mappingBag
                );
            $this
                ->addRepository(
                    $container,
                    $mappingBag
                );
        }

        return $this;
    }

    /**
     * Add entity manager alias.
     *
     * @param ContainerBuilder $container
     * @param MappingBag       $mappingBag
     */
    private function addEntityManager(
        ContainerBuilder $container,
        MappingBag $mappingBag
    ) {
        $definition = new Definition('Doctrine\Common\Persistence\ObjectManager');
        $definition->setFactory([
            new Reference('base.entity_manager_provider'),
            'getEntityManagerByEntityNamespace',
        ]);
        $class = $this->resolveParameterName($container, $mappingBag->getClass());
        $definition->setArguments([$class]);
        $container->setDefinition(
            $mappingBag->getBundle() . '.entity_manager.' . $mappingBag->getName(),
            $definition
        );
    }

    /**
     * Add entity managers aliases.
     *
     * @param ContainerBuilder $container
     * @param MappingBag       $mappingBag
     */
    private function addRepository(
        ContainerBuilder $container,
        MappingBag $mappingBag
    ) {
        $definition = new Definition('Doctrine\Common\Persistence\ObjectRepository');
        $definition->setFactory([
            new Reference('base.entity_repository_provider'),
            'getRepositoryByEntityNamespace',
        ]);
        $class = $this->resolveParameterName($container, $mappingBag->getClass());
        $definition->setArguments([$class]);
        $container->setDefinition(
            $mappingBag->getBundle() . '.entity_repository.' . $mappingBag->getName(),
            $definition
        );
    }
}
