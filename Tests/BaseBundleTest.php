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

namespace Mmoreram\BaseBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\BaseBundle;

/**
 * Class BaseBundleTest.
 */
class BaseBundleTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new FrameworkBundle(),
            new BaseBundle(),
            new DoctrineBundle(),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
        ]);
    }

    /**
     * Test the bundle instances properly.
     */
    public function testBundleInstance()
    {
        $this->assertTrue($this->has('base.object_manager_provider'));
    }
}
