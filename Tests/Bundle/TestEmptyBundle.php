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

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Mmoreram\SymfonyBundleDependencies\DependentBundleInterface;

use Mmoreram\BaseBundle\BaseBundle;

/**
 * Class TestEmptyBundle.
 */
final class TestEmptyBundle extends BaseBundle implements DependentBundleInterface
{
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
            FrameworkBundle::class,
            AnotherBundle::class
        ];
    }
}
