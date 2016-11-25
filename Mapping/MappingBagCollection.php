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

namespace Mmoreram\BaseBundle\Mapping;

/**
 * Class MappingBagCollection.
 */
class MappingBagCollection
{
    /**
     * @var MappingBag[]
     *
     * Mapping bags
     */
    private $mappingBags = [];

    /**
     * Add mapping bag.
     *
     * @param MappingBag $mappingBag
     */
    public function addMappingBag(MappingBag $mappingBag)
    {
        $this->mappingBags[] = $mappingBag;
    }

    /**
     * Get mapping bags.
     *
     * @return MappingBag[]
     */
    public function all() : array
    {
        return $this->mappingBags;
    }

    /**
     * Create by shortcut bloc.
     *
     * @param array  $entities
     * @param string $bundleNamespace
     * @param string $componentNamespace
     * @param string $containerPrefix
     * @param string $managerName
     * @param string $containerObjectManagerName
     * @param string $containerObjectRepositoryName
     * @param bool   $isOverwritable
     *
     * @return MappingBagCollection
     */
    public static function create(
        array $entities,
        string $bundleNamespace,
        string $componentNamespace,
        string $containerPrefix,
        string $managerName = 'default',
        string $containerObjectManagerName = 'object_manager',
        string $containerObjectRepositoryName = 'object_repository',
        bool $isOverwritable = false
    ) : MappingBagCollection {
        $mappingBagCollection = new self();
        foreach ($entities as $entityName => $entityClass) {
            $mappingBagCollection
                ->addMappingBag(new MappingBag(
                    $bundleNamespace,
                    $componentNamespace,
                    $entityName,
                    $entityClass,
                    'Resources/config/doctrine/' . $entityClass . '.orm.yml',
                    $managerName,
                    true,
                    $containerObjectManagerName,
                    $containerObjectRepositoryName,
                    $containerPrefix,
                    $isOverwritable
                ));
        }

        return $mappingBagCollection;
    }
}
