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

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Mapping\MappingBagProvider;
use Mmoreram\BaseBundle\SimpleBaseBundle;

/**
 * Class TestSimpleBundle.
 */
class TestSimpleBundle extends SimpleBaseBundle
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
     * get config files.
     */
    public function getConfigFiles() : array
    {
        return [
            'services',
        ];
    }

    /**
     * get mapping bag provider.
     */
    public function getMappingBagProvider() : ? MappingBagProvider
    {
        return $this->mappingBagProvider;
    }

    /**
     * Create instance of current bundle, and return dependent bundle namespaces.
     *
     * @return array Bundle instances
     */
    public static function getBundleDependencies(KernelInterface $kernel)
    {
        return [
            'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
            'Doctrine\Bundle\DoctrineBundle\DoctrineBundle',
            'Mmoreram\BaseBundle\BaseBundle',
        ];
    }
}
