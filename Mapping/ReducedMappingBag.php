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
 * Class ReducedMappingBag.
 */
class ReducedMappingBag
{
    /**
     * @var string
     *
     * Entity Class
     */
    private $entityClass;

    /**
     * @var string
     *
     * Entity Mapping file
     */
    private $entityMappingFile;

    /**
     * @var string
     *
     * Manager name assigned to the entity
     */
    private $managerName;

    /**
     * @var bool|string
     *
     * Entity is enabled
     */
    private $entityIsEnabled;

    /**
     * ReducedMappingBag constructor.
     *
     * @param string      $entityClass
     * @param string      $entityMappingFile
     * @param string      $managerName
     * @param bool|string $entityIsEnabled
     */
    public function __construct(
        string $entityClass,
        string $entityMappingFile,
        string $managerName,
        string $entityIsEnabled
    ) {
        $this->entityClass = $entityClass;
        $this->entityMappingFile = $entityMappingFile;
        $this->managerName = $managerName;
        $this->entityIsEnabled = $entityIsEnabled;
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
     * Get EntityMappingFile.
     *
     * @return string
     */
    public function getEntityMappingFile(): string
    {
        return $this->entityMappingFile;
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
    public function getEntityIsEnabled()
    {
        return $this->entityIsEnabled;
    }
}
