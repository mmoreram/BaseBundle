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

namespace Mmoreram\BaseBundle\CompilerPass;

/**
 * Class MappingBag.
 */
class MappingBag
{
    /**
     * @var string
     *
     * Bundle
     */
    private $bundle;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var string
     *
     * Manager
     */
    private $manager;

    /**
     * @var string
     *
     * Class
     */
    private $class;

    /**
     * @var string
     *
     * Mapping file
     */
    private $mappingFile;

    /**
     * @var string|bool
     *
     * Enabled
     */
    private $enabled;

    /**
     * @var string
     *
     * Object manager name
     */
    private $objectManagerName;

    /**
     * @var string
     *
     * Object repository name
     */
    private $objectRepositoryName;

    /**
     * MappingBag constructor.
     *
     * @param string      $bundle
     * @param string      $name
     * @param string      $manager
     * @param string      $class
     * @param string      $mappingFile
     * @param string|bool $enabled
     * @param string      $objectManagerName
     * @param string      $objectRepositoryName
     */
    public function __construct(
        string $bundle,
        string $name,
        string $manager,
        string $class,
        string $mappingFile,
        $enabled,
        string $objectManagerName = 'object_manager',
        string $objectRepositoryName = 'object_repository'
    ) {
        $this->bundle = $bundle;
        $this->name = $name;
        $this->manager = $manager;
        $this->class = $class;
        $this->mappingFile = $mappingFile;
        $this->enabled = $enabled;
        $this->objectManagerName = $objectManagerName;
        $this->objectRepositoryName = $objectRepositoryName;
    }

    /**
     * Get Bundle.
     *
     * @return string Bundle
     */
    public function getBundle() : string
    {
        return $this->bundle;
    }

    /**
     * Get Name.
     *
     * @return string Name
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get Manager.
     *
     * @return string Manager
     */
    public function getManager() : string
    {
        return $this->manager;
    }

    /**
     * Get Class.
     *
     * @return string Class
     */
    public function getClass() : string
    {
        return $this->class;
    }

    /**
     * Get MappingFile.
     *
     * @return string MappingFile
     */
    public function getMappingFile() : string
    {
        return $this->mappingFile;
    }

    /**
     * Get Enabled.
     *
     * @return string|bool Enabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get ObjectManagerName.
     *
     * @return string
     */
    public function getObjectManagerName() : string
    {
        return $this->objectManagerName;
    }

    /**
     * Get ObjectRepositoryName.
     *
     * @return string
     */
    public function getObjectRepositoryName() : string
    {
        return $this->objectRepositoryName;
    }
}
