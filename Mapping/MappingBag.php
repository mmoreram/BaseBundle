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

namespace Mmoreram\BaseBundle\Mapping;

/**
 * Class MappingBag.
 */
class MappingBag
{
    /**
     * @var string
     *
     * Bundle namespace. Can be the short mode as well.
     * This bundle is where the mapping files related to the entity are located
     *
     * example: "@MyBundle"
     * example: "MyApp\My\Bundle\MyBundle"
     */
    private $bundleNamespace;

    /**
     * @var string
     *
     * Component namespace.
     * This namespace is where all PHP classes are located
     *
     * example: "MyApp\My\Library\"
     */
    private $componentNamespace;

    /**
     * @var string
     *
     * Entity name, used as an alias.
     *
     * example: "cart"
     */
    private $entityName;

    /**
     * @var string
     *
     * Entity Class, as the name of the PHP class inside the Component's entity
     * folder. The convention is
     *
     * %componentNamespace%/%entityClass%
     *
     * example: "Cart"
     */
    private $entityClass;

    /**
     * @var string
     *
     * Entity Mapping file, where the mapping file is located inside the bundle.
     * The convention is
     *
     * %bundleNamespace%/%entityMappingFile%
     *
     * example: "Resources/config/doctrine/Cart.orm.yml"
     */
    private $entityMappingFile;

    /**
     * @var string
     *
     * Manager name assigned to the entity
     *
     * example: "default"
     */
    private $managerName;

    /**
     * @var bool
     *
     * Entity is enabled
     */
    private $entityIsEnabled;

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
     * Prefix used in container for all constructions in DIC
     */
    private $containerPrefix;

    /**
     * @var bool
     *
     * Is overwritable
     */
    private $isOverwritable;

    /**
     * @var ReducedMappingBag
     *
     * Reduced mapping bag
     */
    private $reducedMappingBag;

    /**
     * MappingBag constructor.
     *
     * @param string $bundleNamespace
     * @param string $componentNamespace
     * @param string $entityName
     * @param string $entityClass
     * @param string $entityMappingFile
     * @param string $managerName
     * @param bool   $entityIsEnabled
     * @param string $containerObjectManagerName
     * @param string $containerObjectRepositoryName
     * @param string $containerPrefix
     * @param bool   $isOverwritable
     */
    public function __construct(
        string $bundleNamespace,
        string $componentNamespace,
        string $entityName,
        string $entityClass,
        string $entityMappingFile,
        string $managerName,
        bool $entityIsEnabled,
        string $containerObjectManagerName,
        string $containerObjectRepositoryName,
        string $containerPrefix,
        bool $isOverwritable
    ) {
        $this->bundleNamespace = $bundleNamespace;
        $this->componentNamespace = $componentNamespace;
        $this->entityName = $entityName;
        $this->entityClass = $entityClass;
        $this->entityMappingFile = $entityMappingFile;
        $this->managerName = $managerName;
        $this->entityIsEnabled = $entityIsEnabled;
        $this->containerObjectManagerName = $containerObjectManagerName;
        $this->containerObjectRepositoryName = $containerObjectRepositoryName;
        $this->containerPrefix = $containerPrefix;
        $this->isOverwritable = $isOverwritable;
        $this->reducedMappingBag = $this->createReducedMappingBag($isOverwritable);
    }

    /**
     * Get BundleNamespace.
     *
     * @return string
     */
    public function getBundleNamespace(): string
    {
        return $this->bundleNamespace;
    }

    /**
     * Get ComponentNamespace.
     *
     * @return string
     */
    public function getComponentNamespace(): string
    {
        return $this->componentNamespace;
    }

    /**
     * Get EntityName.
     *
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * Get EntityClass.
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Get entity namespace.
     *
     * @return string
     */
    public function getEntityNamespace() : string
    {
        return rtrim($this->componentNamespace, '\\') . '\\' . ltrim($this->entityClass, '\\');
    }

    /**
     * Get EntityMappingFile.
     *
     * @return string
     */
    public function getEntityMappingFile(): string
    {
        return $this->entityMappingFile;
    }

    /**
     * Get entity mapping file path.
     *
     * @return string
     */
    public function getEntityMappingFilePath() : string
    {
        return $this->bundleNamespace . '/' . ltrim($this->entityMappingFile, '/');
    }

    /**
     * Get ManagerName.
     *
     * @return string
     */
    public function getManagerName(): string
    {
        return $this->managerName;
    }

    /**
     * Get EntityIsEnabled.
     *
     * @return bool|string
     */
    public function getEntityIsEnabled() : bool
    {
        return $this->entityIsEnabled;
    }

    /**
     * Get ContainerObjectManagerName.
     *
     * @return string
     */
    public function getContainerObjectManagerName(): string
    {
        return $this->containerObjectManagerName;
    }

    /**
     * Get ContainerObjectRepositoryName.
     *
     * @return string
     */
    public function getContainerObjectRepositoryName(): string
    {
        return $this->containerObjectRepositoryName;
    }

    /**
     * Get ContainerPrefix.
     *
     * @return string
     */
    public function getContainerPrefix(): string
    {
        return $this->containerPrefix;
    }

    /**
     * Get IsOverwritable.
     *
     * @return bool
     */
    public function isOverwritable() : bool
    {
        return $this->isOverwritable;
    }

    /**
     * Get ReducingMappingBag.
     *
     * @return ReducedMappingBag
     */
    public function getReducedMappingBag() : ReducedMappingBag
    {
        return $this->reducedMappingBag;
    }

    /**
     * Get entity parametrization by type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getParamFormat(string $type) : string
    {
        return ltrim(($this->containerPrefix . '.entity.' . $this->entityName . '.' . $type), '.');
    }

    /**
     * Create a new instance of ReducedMappingBag given the local information
     * stored.
     *
     * @param bool $isOverwritable
     *
     * @return ReducedMappingBag
     */
    private function createReducedMappingBag(bool $isOverwritable) : ReducedMappingBag
    {
        return new ReducedMappingBag(
            $isOverwritable ? $this->getParamFormat('class') : $this->getEntityNamespace(),
            $isOverwritable ? $this->getParamFormat('mapping_file') : $this->getEntityMappingFilePath(),
            $isOverwritable ? $this->getParamFormat('manager') : $this->managerName,
            $isOverwritable ? $this->getParamFormat('enabled') : $this->entityIsEnabled
        );
    }
}
