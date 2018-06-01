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

namespace Mmoreram\BaseBundle\Tests\Mapping;

use PHPUnit_Framework_TestCase;

use Mmoreram\BaseBundle\Mapping\MappingBagCollection;

/**
 * File header placeholder.
 */
class MappingBagCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test create by crude.
     *
     * @dataProvider dataCreateByCrudeData
     */
    public function testCreateByCrudeData(
        array $entities,
        string $bundleNamespace,
        string $componentNamespace,
        string $containerPrefix,
        bool $isOverwritable,
        array $result
    ) {
        $mappingBagCollection = MappingBagCollection::create(
            $entities,
            $bundleNamespace,
            $componentNamespace,
            $containerPrefix,
            'default',
            'object_manager',
            'object_repository',
            $isOverwritable
        );

        foreach ($mappingBagCollection->all() as $i => $mappingBag) {
            if (!isset($result[$i])) {
                continue;
            }

            $element = $result[$i];

            if (isset($element['bundleNamespace'])) {
                $this->assertEquals($result[$i]['bundleNamespace'], $mappingBag->getBundleNamespace());
            }

            if (isset($element['componentNamespace'])) {
                $this->assertEquals($result[$i]['componentNamespace'], $mappingBag->getComponentNamespace());
            }

            if (isset($element['entityName'])) {
                $this->assertEquals($result[$i]['entityName'], $mappingBag->getEntityName());
            }

            if (isset($element['entityClass'])) {
                $this->assertEquals($result[$i]['entityClass'], $mappingBag->getEntityClass());
            }

            if (isset($element['entityNamespace'])) {
                $this->assertEquals($result[$i]['entityNamespace'], $mappingBag->getEntityNamespace());
            }

            if (isset($element['entityMappingFile'])) {
                $this->assertEquals($result[$i]['entityMappingFile'], $mappingBag->getEntityMappingFile());
            }

            if (isset($element['entityMappingFilePath'])) {
                $this->assertEquals($result[$i]['entityMappingFilePath'], $mappingBag->getEntityMappingFilePath());
            }

            if (isset($element['managerName'])) {
                $this->assertEquals($result[$i]['managerName'], $mappingBag->getManagerName());
            }

            if (isset($element['entityIsEnabled'])) {
                $this->assertEquals($result[$i]['entityIsEnabled'], $mappingBag->getEntityIsEnabled());
            }

            if (isset($element['containerObjectManagerName'])) {
                $this->assertEquals($result[$i]['containerObjectManagerName'], $mappingBag->getContainerObjectManagerName());
            }

            if (isset($element['containerObjectRepositoryName'])) {
                $this->assertEquals($result[$i]['containerObjectRepositoryName'], $mappingBag->getContainerObjectRepositoryName());
            }

            if (isset($element['containerPrefix'])) {
                $this->assertEquals($result[$i]['containerPrefix'], $mappingBag->getContainerPrefix());
            }

            if (isset($element['isOverwritable'])) {
                $this->assertEquals($result[$i]['isOverwritable'], $mappingBag->isOverwritable());
            }

            $reducedMappingBag = $mappingBag->getReducedMappingBag();

            if (isset($element['reducedEntityClass'])) {
                $this->assertEquals($result[$i]['reducedEntityClass'], $reducedMappingBag->getEntityClass());
            }

            if (isset($element['reducedEntityMappingFile'])) {
                $this->assertEquals($result[$i]['reducedEntityMappingFile'], $reducedMappingBag->getEntityMappingFile());
            }

            if (isset($element['reducedManagerName'])) {
                $this->assertEquals($result[$i]['reducedManagerName'], $reducedMappingBag->getManagerName());
            }

            if (isset($element['reducedEntityIsEnabled'])) {
                $this->assertEquals($result[$i]['reducedEntityIsEnabled'], $reducedMappingBag->getEntityIsEnabled());
            }
        }
    }

    /**
     * Data provider.
     */
    public function dataCreateByCrudeData()
    {
        return [
            /*
             * Check all getters.
             */
            [
                [
                    'cart' => 'Cart',
                ],
                '@MyCartBundle',
                '\MyApp\MyCart\Entity',
                'my_app',
                false,
                [
                    [
                        'bundleNamespace' => '@MyCartBundle',
                        'componentNamespace' => '\MyApp\MyCart\Entity',
                        'entityName' => 'cart',
                        'entityClass' => 'Cart',
                        'entityNamespace' => '\MyApp\MyCart\Entity\Cart',
                        'entityMappingFile' => 'Resources/config/doctrine/Cart.orm.yml',
                        'entityMappingFilePath' => '@MyCartBundle/Resources/config/doctrine/Cart.orm.yml',
                        'managerName' => 'default',
                        'entityIsEnabled' => true,
                        'containerObjectManagerName' => 'object_manager',
                        'containerObjectRepositoryName' => 'object_repository',
                        'containerPrefix' => 'my_app',
                        'isOverwritable' => false,
                        'reducedEntityClass' => '\MyApp\MyCart\Entity\Cart',
                        'reducedEntityMappingFile' => '@MyCartBundle/Resources/config/doctrine/Cart.orm.yml',
                        'reducedManagerName' => 'default',
                        'reducedEntityIsEnabled' => true,
                    ],
                ],
            ],

            /*
             * Check special cases.
             */
            [
                [
                    'cart' => 'Cart',
                ],
                '@MyCartBundle',
                '\MyApp\MyCart\Entity\\',
                'my_app',
                false,
                [
                    [
                        'entityNamespace' => '\MyApp\MyCart\Entity\Cart',
                    ],
                ],
            ],

            /*
             * Overwritable.
             */
            [
                [
                    'cart' => 'Cart',
                ],
                '@MyCartBundle',
                '\MyApp\MyCart\Entity',
                'my_app',
                true,
                [
                    [
                        'reducedEntityClass' => 'my_app.entity.cart.class',
                        'reducedEntityMappingFile' => 'my_app.entity.cart.mapping_file',
                        'reducedManagerName' => 'my_app.entity.cart.manager',
                        'reducedEntityIsEnabled' => 'my_app.entity.cart.enabled',
                    ],
                ],
            ],
        ];
    }
}
