<?php

/*
 * This file is part of the Zeus project
 *
 * Copyright (c) 2016 Bet4talent
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Bet4talent amazing team <tech@bet4talent.com>
 */

namespace Mmoreram\BaseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class BaseConfiguration.
 */
abstract class BaseConfiguration implements ConfigurationInterface
{
    /**
     * @var BundleInterface
     *
     * Bundle
     */
    protected $bundle;

    /**
     * @var string
     *
     * Extension name
     */
    protected $extensionName;

    /**
     * Construct method.
     *
     * @var BundleInterface Bundle
     * @var string          $extensionName Extension name
     */
    public function __construct(
        BundleInterface $bundle,
        string $extensionName
    ) {
        $this->bundle = $bundle;
        $this->extensionName = $extensionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->extensionName);
        $this->setupTree($rootNode);

        return $treeBuilder;
    }

    /**
     * Configure the root node.
     *
     * @param ArrayNodeDefinition $rootNode Root node
     */
    abstract protected function setupTree(ArrayNodeDefinition $rootNode);

    /**
     * Add all mapping nodes.
     *
     * @param ArrayNodeDefinition $rootNode      Root node
     * @param array               $entities      Entities
     * @param string              $entityManager Entity Manager
     */
    protected function addMappingNodes(
        ArrayNodeDefinition $rootNode,
        array $entities,
        string $entityManager = 'default'
    ) {
        $mappingNode = $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->addDefaultsIfNotSet()
                    ->children();

        foreach ($entities as $alias => $name) {
            $mappingNode = $mappingNode->append($this->addMappingNode(
                $alias,
                $name,
                $entityManager
            ));
        };
    }

    /**
     * Add a mapping node into configuration.
     *
     * @param string $nodeName      Node name
     * @param string $className     Class name
     * @param string $entityManager Entity Manager
     *
     * @return NodeDefinition Node
     */
    protected function addMappingNode(
        string $nodeName,
        string $className,
        string $entityManager = 'default'
    ) {
        return $this->addCompleteMappingNode(
            $nodeName,
            $this->bundle->getNamespace() . '\Entity\\' . $className,
            '@' . $this->bundle->getName() . '/Resources/config/doctrine/' . $className . '.orm.yml',
            $entityManager,
            true
        );
    }

    /**
     * Add a mapping node into configuration.
     *
     * @param string $nodeName          Node name
     * @param string $entityClass       Class of the entity
     * @param string $entityMappingFile Path of the file where the mapping is defined
     * @param string $entityManager     Name of the entityManager assigned to manage the entity
     * @param bool   $entityEnabled     The entity mapping will be added to the application
     *
     * @return NodeDefinition Node
     */
    protected function addCompleteMappingNode(
        string $nodeName,
        string $entityClass,
        string $entityMappingFile,
        string $entityManager,
        bool $entityEnabled
    ) {
        $builder = new TreeBuilder();
        $node = $builder->root($nodeName);
        $node
            ->treatFalseLike([
                'enabled' => false,
            ])
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('class')
                    ->defaultValue($entityClass)
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('mapping_file')
                    ->defaultValue($entityMappingFile)
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('manager')
                    ->defaultValue($entityManager)
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('enabled')
                    ->defaultValue($entityEnabled)
                ->end()
            ->end()
        ->end();

        return $node;
    }
}