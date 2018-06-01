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

namespace Mmoreram\BaseBundle\Tests\Bundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\BaseBundle;
use Mmoreram\BaseBundle\CompilerPass\MappingCompilerPass;
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingExtension;

/**
 * Class TestMappingBundle.
 */
final class TestMappingBundle extends BaseBundle
{
    /**
     * @var MappingBagProvider
     *
     * Mapping bag provider
     */
    private $mappingBagProvider;

    /**
     * TestMappingBundle constructor.
     *
     * @param MappingBagProvider $mappingBagProvider
     */
    public function __construct(MappingBagProvider $mappingBagProvider)
    {
        $this->mappingBagProvider = $mappingBagProvider;
    }

    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses(): array
    {
        return [
            new MappingCompilerPass($this->mappingBagProvider),
        ];
    }

    /**
     * Return all bundle dependencies.
     *
     * Values can be a simple bundle namespace or its instance
     *
     * @param KernelInterface $kernel
     *
     * @return array
     */
    public static function getBundleDependencies(KernelInterface $kernel): array
    {
        return [
            'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
            'Doctrine\Bundle\DoctrineBundle\DoctrineBundle',
            'Mmoreram\BaseBundle\Tests\Bundle\TestBaseBundle',
            'Mmoreram\BaseBundle\BaseBundle',
        ];
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
        return new TestMappingExtension($this->mappingBagProvider);
    }
}
