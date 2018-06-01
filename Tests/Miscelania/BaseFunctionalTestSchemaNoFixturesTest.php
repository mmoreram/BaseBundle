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

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection\TestStandardMappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\Entity\User;
use Mmoreram\BaseBundle\Tests\Bundle\TestMappingBundle;

/**
 * Class BaseFunctionalTestSchemaNoFixturesTest.
 */
class BaseFunctionalTestSchemaNoFixturesTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            new DoctrineFixturesBundle(),
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
     * Schema must be loaded in all test cases.
     *
     * @return bool
     */
    protected static function loadSchema(): bool
    {
        return true;
    }

    /**
     * Test reload schema.
     */
    public function testReloadSchema()
    {
        $user = new User();
        $user->setName('alehop');
        $this->save($user);
        $this->assertCount(1, $this->findAll('my_prefix:user'));

        $this->reloadSchema();

        $this->assertCount(0, $this->findAll('my_prefix:user'));
    }
}
