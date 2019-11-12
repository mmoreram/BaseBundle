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

namespace Mmoreram\BaseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
     * BaseConfiguration constructor.
     *
     * @param string $extensionAlias
     */
    public function __construct(string $extensionAlias)
    {
        $this->extensionAlias = $extensionAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->extensionAlias);
        $rootNode = $treeBuilder->getRootNode();
        $this->setupTree($rootNode);

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
}
