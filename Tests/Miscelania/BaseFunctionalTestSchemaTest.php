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

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestStandardMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BaseFunctionalTestSchemaTest.
 */
class BaseFunctionalTestSchemaTest extends BaseFunctionalTest
{
    protected static $debug = true;

    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new DoctrineFixturesBundle(),
            new TestMappingBundle(
                new TestStandardMappingBagProvider()
            ),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
        ]);
    }

    /**
     * Load fixtures of these bundles.
     *
     * @return array
     */
    protected static function loadFixturePaths() : array
    {
        return [
            '@TestMappingBundle',
        ];
    }

    /**
     * Test elements loaded from fixtures.
     */
    public function testElementsLoadedFromFixtures()
    {
        $this->assertCount(3, $this->findAll('Mmoreram\BaseBundle\Tests\Bundle\Entity\User'));
    }
}
