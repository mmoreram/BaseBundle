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

namespace Mmoreram\BaseBundle\Tests;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Mapping\MappingBagCollection;
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;
use Mmoreram\BaseBundle\Tests\Bundle\TestSimpleBundle;

/**
 * Class SimpleBaseBundleAnonymousTest.
 */
class SimpleBaseBundleAnonymousTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        return new BaseKernel([
            new TestSimpleBundle(new class() implements MappingBagProvider {
                /**
                 * Get mapping bag collection.
                 *
                 * @return MappingBagCollection
                 */
                public function getMappingBagCollection() : MappingBagCollection
                {
                    return MappingBagCollection::create(
                        ['user' => 'User'],
                        '@TestSimpleBundle',
                        'Mmoreram\BaseBundle\Tests\Bundle\Entity'
                    );
                }
            }),
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/config/providers.yml'],
                ['resource' => '@BaseBundle/Resources/test/doctrine.test.yml'],
            ],
        ]);
    }

    /**
     * Test mapping data.
     */
    public function testMappingData()
    {
        $this->assertTrue($this->has('object_manager.user'));
    }
}
