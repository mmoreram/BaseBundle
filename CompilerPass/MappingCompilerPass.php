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

declare(strict_types=1);

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

            $objectManagerAliasName = $this->addObjectManager(
                $container,
                $mappingBag
            );

            $objectRepositoryAliasName = $this->addObjectRepository(
                $container,
                $mappingBag
            );

            $this->addObjectDirector(
                $container,
                $mappingBag,
                $objectManagerAliasName,
                $objectRepositoryAliasName
            );
        }
    }

    /**
     * Add object manager alias and return the assigned name.
     *
     * @param ContainerBuilder $container
     * @param MappingBag       $mappingBag
     *
     * @return string
     */
    private function addObjectManager(
        ContainerBuilder $container,
        MappingBag $mappingBag
    ): string {
        $reducedMappingBag = $mappingBag->getReducedMappingBag();
        $definition = new Definition('Doctrine\Common\Persistence\ObjectManager');
        $definition->setFactory([
            new Reference('base.object_manager_provider'),
            'getObjectManagerByEntityNamespace',
        ]);
        $class = $this->resolveParameterName($container, $reducedMappingBag->getEntityClass());
        $definition->setArguments([$class]);
        $aliasName = ltrim(($mappingBag->getContainerPrefix().'.'.$mappingBag->getContainerObjectManagerName().'.'.$mappingBag->getEntityName()), '.');
        $container->setDefinition(
            $aliasName,
            $definition
        );

        return $aliasName;
    }

    /**
     * Add object repository aliases and return the assigned name.
     *
     * @param ContainerBuilder $container
     * @param MappingBag       $mappingBag
     *
     * @return string
     */
    private function addObjectRepository(
        ContainerBuilder $container,
        MappingBag $mappingBag
    ): string {
        $reducedMappingBag = $mappingBag->getReducedMappingBag();
        $definition = new Definition('Doctrine\Common\Persistence\ObjectRepository');
        $definition->setFactory([
            new Reference('base.object_repository_provider'),
            'getObjectRepositoryByEntityNamespace',
        ]);
        $class = $this->resolveParameterName($container, $reducedMappingBag->getEntityClass());
        $definition->setArguments([$class]);
        $aliasName = ltrim(($mappingBag->getContainerPrefix().'.'.$mappingBag->getContainerObjectRepositoryName().'.'.$mappingBag->getEntityName()), '.');
        $container->setDefinition(
            $aliasName,
            $definition
        );

        return $aliasName;
    }

    /**
     * Add directors.
     *
     * @param ContainerBuilder $container
     * @param MappingBag       $mappingBag
     * @param string           $objectManagerAliasName
     * @param string           $objectRepositoryAliasName
     */
    private function addObjectDirector(
        ContainerBuilder $container,
        MappingBag $mappingBag,
        string $objectManagerAliasName,
        string $objectRepositoryAliasName
    ) {
        $definition = new Definition('Mmoreram\BaseBundle\ORM\ObjectDirector');
        $definition->setArguments([
            new Reference($objectManagerAliasName),
            new Reference($objectRepositoryAliasName),
        ]);
        $definitionName = ltrim(($mappingBag->getContainerPrefix().'.object_director.'.$mappingBag->getEntityName()), '.');
        $container->setDefinition(
            $definitionName,
            $definition
        );
    }

    /**
     * Return value of parameter name if exists
     * Return itself otherwise.
     *
     * @param ContainerBuilder $container
     * @param mixed            $parameterName
     *
     * @return mixed
     */
    private function resolveParameterName(
        ContainerBuilder $container,
        $parameterName
    ) {
        if (!is_string($parameterName)) {
            return $parameterName;
        }

        return $container->hasParameter($parameterName)
            ? $container->getParameter($parameterName)
            : $parameterName;
    }
}
