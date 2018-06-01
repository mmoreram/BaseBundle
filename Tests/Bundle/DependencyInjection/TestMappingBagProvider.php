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
 * Class TestMappingBagProvider.
 */
class TestMappingBagProvider implements MappingBagProvider
{
    /**
     * @var array
     *
     * Entities
     */
    private $entities;

    /**
     * @var string
     *
     * Bundle namespace
     */
    private $bundleNamespace;

    /**
     * @var string
     *
     * Component namespace
     */
    private $componentNamespace;

    /**
     * @var string
     *
     * container prefix
     */
    private $containerPrefix;

    /**
     * @var string
     *
     * Manager name
     */
    private $managerName;

    /**
     * @var string
     *
     * Object manager name inside container. By this name, the entity manager
     * assigned to this entity will be accessible using this convention
     *
     * %containerPrefix%.%containerObjectManagerName%.%entityName%
     *
     * example: "object_manager%
     */
    private $containerObjectManagerName;

    /**
     * @var string
     *
     * Object repository name inside container. By this name, the entity
     * repository assigned to this entity will be accessible using this
     * convention
     *
     * %containerPrefix%.%containerObjectRepositoryName%.%entityName%
     *
     * example: "object_repository%
     */
    private $containerObjectRepositoryName;

    /**
     * @var string
     *
     * Is overwritable
     */
    private $isOverwritable;

    /**
     * Create by shortcut bloc.
     *
     * @param array     $entities
     * @param string    $bundleNamespace
     * @param string    $componentNamespace
     * @param string    $containerPrefix
     * @param string    $managerName
     * @param string    $containerObjectManagerName
     * @param string    $containerObjectRepositoryName
     * @param bool|null $isOverwritable
     */
    public function __construct(
        array $entities,
        string $bundleNamespace,
        string $componentNamespace,
        string $containerPrefix = '',
        string $managerName = 'default',
        string $containerObjectManagerName = 'object_manager',
        string $containerObjectRepositoryName = 'object_repository',
        bool $isOverwritable = false
    ) {
        $this->entities = $entities;
        $this->bundleNamespace = $bundleNamespace;
        $this->componentNamespace = $componentNamespace;
        $this->containerPrefix = $containerPrefix;
        $this->managerName = $managerName;
        $this->containerObjectManagerName = $containerObjectManagerName;
        $this->containerObjectRepositoryName = $containerObjectRepositoryName;
        $this->isOverwritable = $isOverwritable;
    }

    /**
     * Get mapping bag collection.
     *
     * @return MappingBagCollection
     */
    public function getMappingBagCollection(): MappingBagCollection
    {
        return MappingBagCollection::create(
            $this->entities,
            $this->bundleNamespace,
            $this->componentNamespace,
            $this->containerPrefix,
            $this->managerName,
            $this->containerObjectManagerName,
            $this->containerObjectRepositoryName,
            $this->isOverwritable
        );
    }
}
