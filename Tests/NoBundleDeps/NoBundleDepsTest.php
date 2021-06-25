<?php

/*
 * This file is part of the BaseBundle for Symfony.
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
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\Bundle\AnotherBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class NoBundleDepsTest.
 */
class NoBundleDepsTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            FrameworkBundle::class,
            AnotherBundle::class,
        ], [
            'parameters' => [
                'kernel.secret' => '1234',
            ],
            'framework' => [
                'test' => true,
            ],
        ]);
    }

    /**
     * Test dependencies.
     */
    public function testDependencies()
    {
        $this->expectNotToPerformAssertions();
        static::$kernel->getBundle('AnotherBundle');
    }
}
