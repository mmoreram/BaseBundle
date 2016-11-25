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
    protected function getKernel()
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
            'doctrine' => [
                'dbal' => [
                    'connections' => [
                        'default' => [
                            'driver' => 'pdo_sqlite',
                            'dbname' => 'test.sqlite',
                            'path' => '%kernel.root_dir%/cache/test/test.sqlite',
                            'memory' => true,
                            'charset' => 'UTF8',
                        ],
                    ],
                ],
                'orm' => [
                    'entity_managers' => [
                        'default' => [
                            'connection' => 'default',
                            'auto_mapping' => false,
                            'metadata_cache_driver' => [],
                            'query_cache_driver' => [],
                            'result_cache_driver' => [],
                        ],
                    ],
                ],
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
     */
    protected function setUp()
    {
        try {
            parent::setUp();
        } catch (RuntimeException $e) {
            return true;
        }

        $this->fail('This test should fail as some configuration is defined while should\'t be possible');
    }

    /**
     * force kernel creation.
     */
    public function testKernelCreation()
    {
    }
}
