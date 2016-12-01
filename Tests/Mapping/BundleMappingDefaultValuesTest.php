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
 * Class BundleMappingDefaultValues.
 */
class BundleMappingDefaultValuesTest extends BaseFunctionalTest
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
                'Mmoreram\BaseBundle\Tests\Bundle\Entity'
            )),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/framework.test.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
            'parameters' => [
                'rand' => 'rueyiw',
            ],
        ]);
    }

    /**
     * Test bundle all expected services and parameters.
     */
    public function testDefaultValues()
    {
        $this->assertEquals(
            'Mmoreram\BaseBundle\Tests\Bundle\Entity\User',
            $this->getParameter('entity.user.class')
        );

        $this->assertEquals(
            '@TestMappingBundle/Resources/config/doctrine/User.orm.yml',
            $this->getParameter('entity.user.mapping_file')
        );

        $this->assertEquals(
            'default',
            $this->getParameter('entity.user.manager')
        );

        $this->assertTrue($this->getParameter('entity.user.enabled'));

        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectManager',
            $this->get('object_manager.user')
        );

        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectRepository',
            $this->get('object_repository.user')
        );
    }
}
