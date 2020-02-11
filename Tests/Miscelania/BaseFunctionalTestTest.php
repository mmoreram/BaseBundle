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

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Kernel\BaseKernel;
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\Bundle\TestClass;
use Mmoreram\BaseBundle\Tests\Bundle\TestEmptyBundle;

/**
 * Class BaseFunctionalTestTest.
 */
class BaseFunctionalTestTest extends BaseFunctionalTest
{
    /**
     * @var bool
     *
     * Debug mode
     */
    protected static $debug = true;

    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            TestEmptyBundle::class,
        ], [
            'imports' => [
                ['resource' => '@TestEmptyBundle/Resources/config/services.yml'],
            ],
            'parameters' => [
                'kernel.secret' => '1234',
            ],
        ]);
    }

    /**
     * Test dependencies.
     */
    public function testDependencies()
    {
        $bundle = static::$kernel->getBundle('AnotherBundle');
        $this->assertInstanceof(Bundle::class, $bundle);
    }

    /**
     * Test get.
     */
    public function testGet()
    {
        $this->assertInstanceOf(
            TestClass::class,
            $this->get('test.service')
        );

        $this->assertEquals('value1', $this->get('test.service')->getName());
    }

    /**
     * Test has.
     */
    public function testHas()
    {
        $this->assertTrue($this->has('test.service'));
    }

    /**
     * Test get parameter.
     */
    public function testGetParameter()
    {
        $this->assertEquals(
            '1234',
            $this->getParameter('kernel.secret')
        );
    }
}
