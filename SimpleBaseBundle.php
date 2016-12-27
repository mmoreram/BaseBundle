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

namespace Mmoreram\BaseBundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Mmoreram\BaseBundle\CompilerPass\MappingCompilerPass;
use Mmoreram\BaseBundle\DependencyInjection\SimpleBaseExtension;
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;

/**
 * Class AbstractBundle.
 */
class SimpleBaseBundle extends BaseBundle
{
    /**
     * get config files.
     */
    public function getConfigFiles() : array
    {
        return [];
    }

    /**
     * get mapping bag provider.
     */
    public function getMappingBagProvider() : ? MappingBagProvider
    {
        return null;
    }

    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        return new SimpleBaseExtension(
            $this,
            $this->getConfigFiles(),
            $this->getMappingBagProvider()
        );
    }

    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        $mappingBagProvider = $this->getMappingBagProvider();

        return $mappingBagProvider instanceof MappingBagProvider
            ? array_merge(
                parent::getCompilerPasses(),
                [new MappingCompilerPass($mappingBagProvider)]
            )
            : parent::getCompilerPasses();
    }
}
