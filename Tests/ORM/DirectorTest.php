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

namespace Mmoreram\BaseBundle\Tests\ORM;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\ORM\ObjectDirector;
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\Entity\User;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * File header placeholder.
 */
class DirectorTest extends BaseFunctionalTest
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
     * Schema must be loaded in all test cases.
     *
     * @return bool
     */
    protected static function loadSchema(): bool
    {
        return true;
    }

    /**
     * Test director behavior.
     */
    public function testDirectorBehavior()
    {
        $this->assertTrue($this->has('object_director.user'));
        $this->assertInstanceof(
            ObjectDirector::class,
            $this->get('object_director.user')
        );
        $director = $this->get('object_director.user');
        $this->assertNull($director->find(1));

        $user = new User('1', 'Marc');
        $director->save($user);

        $this->assertNotNull($director->find(1));
        $director->remove($user);
        $this->assertNull($director->find(1));
    }
}
