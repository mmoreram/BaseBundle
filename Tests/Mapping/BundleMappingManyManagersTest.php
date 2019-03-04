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

namespace Mmoreram\BaseBundle\Tests\Mapping;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\Entity\User;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BundleMappingManyManagersTest.
 */
class BundleMappingManyManagersTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            new TestMappingBundle(new TestMappingBagProvider(
                [
                    'user' => 'User',
                    'another_user' => 'AnotherUser',
                ],
                '@TestMappingBundle',
                'Mmoreram\BaseBundle\Tests\Bundle\Entity',
                'my_prefix',
                'default',
                'object_manager',
                'object_repository',
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
            'test' => [
                'mapping' => [
                    'another_user' => [
                        'manager' => 'another_manager',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Schema must be loaded in all test cases.
     *
     * @return bool
     */
    protected static function loadSchema(): bool
    {
        return true;
    }

    /**
     * Test bundle all expected services and parameters.
     */
    public function testHasExtension()
    {
        $user = new User('1', 'Sara');
        $this->save($user);

        $user = new User('2', 'Pacho');
        $this->save($user);

        $this->assertTrue(true);
    }
}
