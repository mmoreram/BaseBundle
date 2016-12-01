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

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;

/**
 * Class BundleWithoutExtensionTest.
 */
class BundleWithoutExtensionTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            'Mmoreram\BaseBundle\Tests\Bundle\TestEmptyBundle',
        ]);
    }

    /**
     * Test bundle has no extension.
     */
    public function testHasNoExtension()
    {
        $this->assertFalse($this->has('test.service'));
    }
}
