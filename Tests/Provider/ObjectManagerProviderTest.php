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

namespace Mmoreram\BaseBundle\Test\Provider;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\TestEntityBundle;

/**
 * Class ObjectManagerProviderTest.
 */
class ObjectManagerProviderTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            new TestEntityBundle(),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
            'services' => [
                'base.entity_manager.user' => [
                    'parent' => 'base.abstract_object_manager',
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
            'Doctrine\Common\Persistence\ObjectManager',
            $this->get('base.entity_manager.user')
        );
    }
}
