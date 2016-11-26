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

namespace Mmoreram\BaseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Mmoreram\BaseBundle\Mapping\MappingBagCollection;
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;

/**
 * Class BaseConfiguration.
 */
class BaseConfiguration implements ConfigurationInterface
{
    /**
     * @var string
     *
     * Extension alias
     */
    private $extensionAlias;

    /**
     * @var null|MappingBagProvider
     *
     * Mapping bag provider
     */
    private $mappingBagProvider;

    /**
     * BaseConfiguration constructor.
     *
     * @param string             $extensionAlias
     * @param MappingBagProvider $mappingBagProvider
     */
    public function __construct(
        string $extensionAlias,
        MappingBagProvider $mappingBagProvider = null
    ) {
        $this->extensionAlias = $extensionAlias;
        $this->mappingBagProvider = $mappingBagProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->extensionAlias);
        $this->setupTree($rootNode);

        if ($this->mappingBagProvider instanceof MappingBagProvider) {
            $this->addDetectedMappingNodes(
                $rootNode,
                $this
                    ->mappingBagProvider
                    ->getMappingBagCollection()
            );
        }

        return $treeBuilder;
    }

    /**
     * Configure the root node.
     *
     * @param ArrayNodeDefinition $rootNode Root node
     */
    protected function setupTree(ArrayNodeDefinition $rootNode)
    {
        // Silent pass. Nothing to do here by default
    }

    /**
     * Add a mapping node into configuration.
     *
     * @param string $nodeName
     * @param string $entityClass
     * @param string $entityMappingFile
     * @param string $entityManager
     * @param bool   $entityEnabled
     *
     * @return NodeDefinition Node
     */
    protected function addMappingNode(
        string $nodeName,
        string $entityClass,
        string $entityMappingFile,
        string $entityManager,
        bool $entityEnabled
    ) : NodeDefinition {
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

    /**
     * Add all mapping nodes injected in the mappingBagCollection.
     *
     * @param ArrayNodeDefinition  $rootNode
     * @param MappingBagCollection $mappingBagCollection
     */
    private function addDetectedMappingNodes(
        ArrayNodeDefinition $rootNode,
        MappingBagCollection $mappingBagCollection
    ) {
        $mappingNode = $rootNode
            ->children()
            ->arrayNode('mapping')
            ->addDefaultsIfNotSet()
            ->children();

        foreach ($mappingBagCollection->all() as $mappingBag) {
            if ($mappingBag->isOverwritable()) {
                $mappingNode->append($this->addMappingNode(
                    $mappingBag->getEntityName(),
                    $mappingBag->getEntityNamespace(),
                    $mappingBag->getEntityMappingFilePath(),
                    $mappingBag->getManagerName(),
                    $mappingBag->getEntityIsEnabled()
                ));
            }
        }
    }
}
