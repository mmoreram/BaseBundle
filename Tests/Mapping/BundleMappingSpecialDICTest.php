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

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BundleMappingSpecialDICTest.
 */
class BundleMappingSpecialDICTest extends BaseFunctionalTest
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
                'another_prefix',
                'another_entity_manager',
                'manager',
                'repository',
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
                        'another_entity_manager' => [
                            'connection' => 'default',
                            'auto_mapping' => false,
                            'metadata_cache_driver' => [],
                            'query_cache_driver' => [],
                            'result_cache_driver' => [],
                        ],
                    ],
                ],
            ],
            'imports' => [
                [
                    'resource' => __DIR__ . '/../../Resources/config/providers.yml',
                ],
            ],
        ]);
    }

    /**
     * Test providers generation.
     */
    public function testProviderAutogeneration()
    {
        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectManager',
            $this->get('another_prefix.manager.user')
        );

        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectRepository',
            $this->get('another_prefix.repository.user')
        );
    }
}
