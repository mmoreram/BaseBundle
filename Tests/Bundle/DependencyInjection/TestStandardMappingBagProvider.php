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

namespace Mmoreram\BaseBundle\Tests\Bundle\DependencyInjection;

use Mmoreram\BaseBundle\Mapping\MappingBagCollection;
use Mmoreram\BaseBundle\Mapping\MappingBagProvider;

/**
 * Class TestStandardMappingBagProvider.
 */
class TestStandardMappingBagProvider implements MappingBagProvider
{
    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection(): MappingBagCollection
    {
        return MappingBagCollection::create(
            ['user' => 'User'],
            '@TestBaseBundle',
            'Mmoreram\BaseBundle\Tests\Bundle\Entity',
            'my_prefix',
            'default',
            'object_manager',
            'object_repository',
            false
        );
    }
}
