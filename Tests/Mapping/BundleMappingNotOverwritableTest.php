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

use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BundleMappingNotOverwritableTest.
 */
class BundleMappingNotOverwritableTest extends BaseFunctionalTest
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
                ['user' => 'User'],
                '@TestMappingBundle',
                'Mmoreram\BaseBundle\Tests\Bundle\Entity',
                'my_prefix',
                'default',
                'object_manager',
                'repository_manager',
                false
            )),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
            'test' => [
                'mapping' => [
                    'user' => [
                        'enabled' => false,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @throws RuntimeException unable to start the application
     */
    public static function setUpBeforeClass()
    {
        try {
            parent::setUpBeforeClass();
        } catch (RuntimeException $e) {
            return true;
        }

        self::fail('This test should fail as some configuration is defined while should\'t be possible');
    }

    /**
     * force kernel creation.
     */
    public function testKernelCreation()
    {
        $this->assertTrue(true);
    }
}
