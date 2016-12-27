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

namespace Mmoreram\BaseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

use Mmoreram\BaseBundle\Mapping\MappingBagProvider;

/**
 * Class SimpleBaseExtension.
 */
class SimpleBaseExtension extends BaseExtension
{
    /**
     * @var BundleInterface
     *
     * Bundle
     */
    private $bundle;

    /**
     * @var array
     *
     * Config files
     */
    private $configFiles;

    /**
     * SimpleBaseExtension constructor.
     *
     * @param BundleInterface    $bundle
     * @param array              $configFiles
     * @param MappingBagProvider $mappingBagProvider
     */
    public function __construct(
        BundleInterface $bundle,
        array $configFiles,
        MappingBagProvider $mappingBagProvider = null)
    {
        parent::__construct($mappingBagProvider);

        $this->bundle = $bundle;
        $this->configFiles = $configFiles;
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return strtolower(
            preg_replace(
                '/(?<!^)[A-Z]/',
                '_$0',
                preg_replace(
                    '~Bundle$~',
                    '',
                    $this->bundle->getName()
                )
            )
        );
    }

    /**
     * Get the Config file location.
     *
     * @return string
     */
    protected function getConfigFilesLocation() : string
    {
        return $this
            ->bundle
            ->getPath() . '/Resources/config';
    }

    /**
     * Config files to load.
     *
     * Each array position can be a simple file name if must be loaded always,
     * or an array, with the filename in the first position, and a boolean in
     * the second one.
     *
     * As a parameter, this method receives all loaded configuration, to allow
     * setting this boolean value from a configuration value.
     *
     * return array(
     *      'file1.yml',
     *      'file2.yml',
     *      ['file3.yml', $config['my_boolean'],
     *      ...
     * );
     *
     * @param array $config Config definitions
     *
     * @return array Config files
     */
    protected function getConfigFiles(array $config) : array
    {
        return $this->configFiles;
    }

    /**
     * Return a new Configuration instance.
     *
     * If object returned by this method is an instance of
     * ConfigurationInterface, extension will use the Configuration to read all
     * bundle config definitions.
     *
     * Also will call getParametrizationValues method to load some config values
     * to internal parameters.
     *
     * @return ConfigurationInterface|null
     */
    protected function getConfigurationInstance() : ? ConfigurationInterface
    {
        return $this->mappingBagProvider
            ? new BaseConfiguration(
                $this->getAlias(),
                $this->mappingBagProvider
            )
            : null;
    }
}
