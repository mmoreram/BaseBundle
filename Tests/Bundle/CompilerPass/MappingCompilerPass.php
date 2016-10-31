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

namespace Mmoreram\BaseBundle\Tests\Bundle\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Mmoreram\BaseBundle\CompilerPass\MappingBag;
use Mmoreram\BaseBundle\CompilerPass\MappingBagCollection;
use Mmoreram\BaseBundle\CompilerPass\MappingCompilerPass as MainMappingCompilerPass;

/**
 * Class MappingCompilerPass.
 */
class MappingCompilerPass extends MainMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $mappingBagCollection = new MappingBagCollection();

        $mappingBagCollection->addMappingBag(
            new MappingBag(
                'app',
                'user',
                'default',
                'Mmoreram\BaseBundle\Tests\Bundle\Entity\User',
                '@TestEntityBundle/Resources/config/doctrine/User.orm.yml',
                true
            )
        );

        $this->addEntityMappings(
            $container,
            $mappingBagCollection
        );
    }
}
