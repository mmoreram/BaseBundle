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

namespace Mmoreram\BaseBundle\Tests;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Provider\ObjectManagerProvider;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\TestSimpleBundle;

/**
 * Class SimpleBaseBundleTest.
 */
class SimpleBaseBundleTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            new TestSimpleBundle(new TestMappingBagProvider(
                ['user' => 'User'],
                '@TestSimpleBundle',
                'Mmoreram\BaseBundle\Tests\Bundle\Entity',
                '',
                'default',
                'doctrine_manager',
                'repo',
                true
            )),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
            'doctrine' => [
                'orm' => [
                    'entity_managers' => [
                        'default' => [
                            'connection' => 'default',
                            'auto_mapping' => false,
                        ],
                        'another_manager' => [
                            'connection' => 'default',
                            'auto_mapping' => false,
                        ],
                    ],
                ],
            ],
            'test_simple' => [
                'mapping' => [
                    'user' => [
                        'manager' => 'another_manager',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test the bundle instances properly.
     */
    public function testBundleInstance()
    {
        $this->assertTrue($this->has('base.object_manager_provider'));
        $this->assertInstanceOf(ObjectManagerProvider::class, $this->get('base.object_manager_provider'));
    }

    /**
     * Test mapping data.
     */
    public function testMappingData()
    {
        $this->assertTrue($this->has('doctrine_manager.user'));
        $this->assertTrue($this->has('repo.user'));
        $this->assertSame(
            $this->get('doctrine_manager.user'),
            $this->get('doctrine.orm.another_manager_entity_manager')
        );
    }
}
