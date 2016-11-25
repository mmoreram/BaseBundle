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

namespace Mmoreram\BaseBundle\Test\Provider;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\TestEntityBundle;

/**
 * Class ObjectRepositoryProviderTest.
 */
class ObjectRepositoryProviderTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return new BaseKernel([
            new TestEntityBundle(),
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
            'imports' => [
                [
                    'resource' => __DIR__ . '/../../Resources/config/providers.yml',
                ],
            ],
            'services' => [
                'base.repository.user' => [
                    'parent' => 'base.abstract_object_repository',
                    'arguments' => [
                        'Mmoreram\BaseBundle\Tests\Bundle\Entity\User',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test repository availability.
     */
    public function testRepositoryExists()
    {
        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectRepository',
            $this->get('base.repository.user')
        );
    }
}
