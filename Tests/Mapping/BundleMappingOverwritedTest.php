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

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BundleMappingOverwritedTest.
 */
class BundleMappingOverwritedTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new TestMappingBundle(new TestMappingBagProvider(
                ['user' => 'User'],
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
                            'metadata_cache_driver' => [],
                            'query_cache_driver' => [],
                            'result_cache_driver' => [],
                        ],
                        'another_manager' => [
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
                        'class' => 'Mmoreram\BaseBundle\Tests\Bundle\Entity\AnotherUser',
                        'mapping_file' => '@TestMappingBundle/Resources/config/doctrine/AnotherUser.orm.yml',
                        'manager' => 'another_manager',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test bundle all expected services and parameters.
     */
    public function testHasExtension()
    {
        $this->assertEquals(
            'Mmoreram\BaseBundle\Tests\Bundle\Entity\AnotherUser',
            $this->getParameter('my_prefix.entity.user.class')
        );

        $this->assertEquals(
            '@TestMappingBundle/Resources/config/doctrine/AnotherUser.orm.yml',
            $this->getParameter('my_prefix.entity.user.mapping_file')
        );

        $this->assertEquals(
            'another_manager',
            $this->getParameter('my_prefix.entity.user.manager')
        );

        $this->assertTrue($this->getParameter('my_prefix.entity.user.enabled'));

        /**
         * @var ObjectManager $manager
         */
        $manager = $this->get('my_prefix.object_manager.user');
        $entityClass = $this->getParameter('my_prefix.entity.user.class');

        $this->assertEquals(
            $entityClass,
            $manager->getClassMetadata($entityClass)->getName()
        );

        $this->assertSame(
            $manager->getRepository($entityClass),
            $this->get('my_prefix.object_repository.user')
        );
    }
}
