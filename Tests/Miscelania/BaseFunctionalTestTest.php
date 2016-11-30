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

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestStandardMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\Entity\User;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BaseFunctionalTestTest.
 */
class BaseFunctionalTestTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
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
     * Test get.
     */
    public function testGet()
    {
        $this->assertInstanceOf(
            'Mmoreram\BaseBundle\Tests\Bundle\TestClass',
            $this->get('test.service')
        );
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
            'en',
            $this->getParameter('secret')
        );
    }

    /**
     * Test get object manager.
     */
    public function testGetObjectManager()
    {
        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectManager',
            $this->getObjectManager('Mmoreram\BaseBundle\Tests\Bundle\Entity\User')
        );

        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectManager',
            $this->getObjectManager('BaseBundle:User')
        );
    }

    /**
     * Test get object repository.
     */
    public function testGetObjectRepository()
    {
        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectRepository',
            $this->getObjectRepository('Mmoreram\BaseBundle\Tests\Bundle\Entity\User')
        );

        $this->assertInstanceOf(
            'Doctrine\Common\Persistence\ObjectRepository',
            $this->getObjectRepository('BaseBundle:User')
        );
    }

    /**
     * Test find.
     */
    public function testFind()
    {
        $user1 = new User();
        $user1->setName('Marc');
        $this->save($user1);

        $this->assertEquals(
            1,
            $this->find('BaseBundle:User', 1)
        );
    }

    /**
     * Test find all.
     */
    public function testFindAll()
    {
        $user1 = new User();
        $user1->setName('Marc');
        $this->save($user1);

        $user2 = new User();
        $user2->setName('India');
        $this->save($user2);

        $this->assertCount(
            2,
            $this->findAll('BaseBundle:User')
        );
    }

    /**
     * Test save.
     */
    public function testSave()
    {
        $user1 = new User();
        $user1->setName('Marc');

        $this->save($user1);
        $this->assertNotNull($user1->getId());

        $user1->setName('India');
        $user2 = new User();
        $user2->setName('Sara');

        $user3 = new User();
        $user3->setName('Yepa');

        $this->save([
            $user1,
            $user2,
            $user3,
        ]);

        $this->assertEquals('India', $this->find('BaseBundle:User', 1));
        $this->assertEquals('Sara', $this->find('BaseBundle:User', 2));
        $this->assertEquals('Yepa', $this->find('BaseBundle:User', 3));
    }
}
