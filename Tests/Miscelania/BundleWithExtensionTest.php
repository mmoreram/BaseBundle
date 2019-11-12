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

use Mmoreram\BaseBundle\Kernel\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\TestBundle;
use Mmoreram\BaseBundle\Tests\Bundle\TestClass;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;

/**
 * Class BundleWithExtensionTest.
 */
class BundleWithExtensionTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            TestBundle::class,
        ]);
    }

    /**
     * Test get.
     */
    public function testGet()
    {
        $this->assertInstanceOf(
            TestClass::class,
            $this->get('test.service2')
        );

        $this->assertEquals('value2', $this->get('test.service2')->getName());
    }
}
