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

namespace Mmoreram\BaseBundle\Kernel;

use Mmoreram\BaseBundle\Dependencies\BundleDependenciesResolver;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Trait BaseKernelTrait.
 */
trait BaseKernelTrait
{
    use MicroKernelTrait;
    use BundleDependenciesResolver;

    /**
     * @var string[]
     *
     * Bundle array
     */
    private $bundlesToLoad;

    /**
     * @var array[]
     *
     * Configuration
     */
    private $configuration;

    /**
     * @var array[]
     *
     * Routes
     */
    private $routes;

    /**
     * @var string
     *
     * Root dir prefix
     */
    private $rootDirPrefix;

    /**
     * BaseKernel constructor.
     *
     * @param string[] $bundlesToLoad
     * @param array[]  $configuration
     * @param array[]  $routes
     * @param string   $environment
     * @param bool     $debug
     * @param string   $rootDirPrefix
     */
    public function __construct(
        array $bundlesToLoad,
        array $configuration = [],
        array $routes = [],
        string $environment = 'test',
        bool $debug = false,
        string $rootDirPrefix = null
    ) {
        $this->rootDirPrefix = $rootDirPrefix;
        $this->bundlesToLoad = $bundlesToLoad;
        $this->routes = $routes;
        $this->configuration = array_merge(
            [
                'parameters' => [
                    'kernel.secret' => '1234',
                ],
            ],
            $configuration
        );

        parent::__construct($environment, $debug);
    }

    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles()
    {
        return $this->getBundleInstances(
            $this,
            $this->bundlesToLoad
        );
    }

    /**
     * Configures the container.
     *
     * You can register extensions:
     *
     * $c->loadFromExtension('framework', array(
     *     'secret' => '%secret%'
     * ));
     *
     * Or services:
     *
     * $c->register('halloween', 'FooBundle\HalloweenProvider');
     *
     * Or parameters:
     *
     * $c->setParameter('halloween', 'lot of fun');
     *
     * @param ContainerBuilder $c
     * @param LoaderInterface  $loader
     */
    protected function configureContainer(
        ContainerBuilder $c,
        LoaderInterface $loader
    ) {
        $yamlContent = Yaml::dump($this->configuration);
        $filePath = sys_get_temp_dir().'/base-test-'.rand(1, 9999999).'.yml';
        file_put_contents($filePath, $yamlContent);
        $loader->load($filePath);
        unlink($filePath);
    }

    /**
     * Add or import routes into your application.
     *
     *     $routes->import('config/routing.yml');
     *     $routes->add('/admin', 'AppBundle:Admin:dashboard', 'admin_dashboard');
     *
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        foreach ($this->routes as $route) {
            is_array($route)
                ? $routes->add(
                    $route[0],
                    $route[1],
                    $route[2]
                )
                : $routes->import($route);
        }
    }

    /**
     * Gets the application root dir.
     *
     * @return string The application root dir
     */
    public function getRootDir()
    {
        return $this->getProjectDir();
    }

    /**
     * Sort array's first level, taking in account if associative array or
     * sequential array.
     *
     * @param mixed $element
     */
    private function sortArray(&$element)
    {
        if (is_array($element)) {
            array_walk($element, [$this, 'sortArray']);
            array_key_exists(0, $element)
                ? sort($element)
                : ksort($element);
        }
    }

    /**
     * Gets the application root dir (path of the project's composer file).
     *
     * @return string The project root dir
     */
    public function getProjectDir()
    {
        if (!is_null($this->rootDirPrefix)) {
            return $this->rootDirPrefix;
        }

        $kernelHash = hash(
            'md5',
            json_encode($this->toArray())
        );

        $possibleComposerPath = parent::getProjectDir() . '/../../..';
        return ((file_exists($possibleComposerPath . '/composer.json'))
            ? $possibleComposerPath
            : parent::getProjectDir()) . '/var/test/' . $kernelHash;
    }

    /**
     * Get kernel as array
     *
     * @return array
     */
    public function toArray() : array
    {
        $routes = $this->routes;
        $bundles = $this->bundlesToLoad;
        sort($bundles);
        sort($routes);
        $config = $this->configuration;
        $this->sortArray($config);

        return [
            'namespace' => get_class($this),
            'bundles' => array_map(function ($bundle) {
                return is_object($bundle)
                    ? get_class($bundle)
                    : $bundle;
            }, $bundles),
            'config' => $config,
            'routes' => $routes,
        ];
    }

    /**
     * Create kernel from array
     *
     * @param array $data
     * @param string   $environment
     * @param bool     $debug
     * @param string   $rootDirPrefix
     *
     * @return object
     */
    public static function createFromArray(
        array $data,
        string $environment = 'test',
        bool $debug = false,
        string $rootDirPrefix = null
    )
    {
        $namespace = $data['namespace'];

        return new $namespace(
            $data['bundles'],
            $data['config'],
            $data['routes'],
            $environment,
            $debug,
            $rootDirPrefix
        );
    }
}
