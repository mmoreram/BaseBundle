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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Mmoreram\BaseBundle\Mapping\MappingBag;
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;
use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;

/**
 * Class MappingCompilerPass.
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * @var MappingBagProvider
     *
     * Mapping bag provider
     */
    private $mappingBagProvider;

    /**
     * MappingCompilerPass constructor.
     *
     * @param MappingBagProvider $mappingBagProvider
     */
    public function __construct(MappingBagProvider $mappingBagProvider)
    {
        $this->mappingBagProvider = $mappingBagProvider;
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addEntityMappings(
            $container,
            $this->mappingBagProvider
        );
    }

    /**
     * Add entity mapping given the entity name, given that all entity
     * definitions are built the same way and given as well that the method
     * addEntityMapping exists and is accessible.
     *
     * @param ContainerBuilder   $container
     * @param MappingBagProvider $mappingBagProvider
     */
    private function addEntityMappings(
        ContainerBuilder $container,
        MappingBagProvider $mappingBagProvider
    ) {
        $mappingBagCollection = $mappingBagProvider->getMappingBagCollection();

        foreach ($mappingBagCollection->all() as $mappingBag) {
            $reducedMappingBag = $mappingBag->getReducedMappingBag();
            $this
                ->addEntityMapping(
                    $container,
                    $reducedMappingBag->getManagerName(),
                    $reducedMappingBag->getEntityClass(),
                    $reducedMappingBag->getEntityMappingFile(),
                    $reducedMappingBag->getEntityIsEnabled()
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
        $reducedMappingBag = $mappingBag->getReducedMappingBag();
        $definition = new Definition('Doctrine\Common\Persistence\ObjectManager');
        $definition->setFactory([
            new Reference('base.object_manager_provider'),
            'getObjectManagerByEntityNamespace',
        ]);
        $class = $this->resolveParameterName($container, $reducedMappingBag->getEntityClass());
        $definition->setArguments([$class]);
        $container->setDefinition(
            ltrim(($mappingBag->getContainerPrefix() . '.' . $mappingBag->getContainerObjectManagerName() . '.' . $mappingBag->getEntityName()), '.'),
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
        $reducedMappingBag = $mappingBag->getReducedMappingBag();
        $definition = new Definition('Doctrine\Common\Persistence\ObjectRepository');
        $definition->setFactory([
            new Reference('base.object_repository_provider'),
            'getObjectRepositoryByEntityNamespace',
        ]);
        $class = $this->resolveParameterName($container, $reducedMappingBag->getEntityClass());
        $definition->setArguments([$class]);
        $container->setDefinition(
            ltrim(($mappingBag->getContainerPrefix() . '.' . $mappingBag->getContainerObjectRepositoryName() . '.' . $mappingBag->getEntityName()), '.'),
            $definition
        );
    }
}
