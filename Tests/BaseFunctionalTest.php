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

namespace Mmoreram\BaseBundle\Tests;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Exception;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class BaseFunctionalTest.
 */
abstract class BaseFunctionalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     *
     * application
     */
    protected static $application;

    /**
     * @var KernelInterface
     *
     * kernel being used
     */
    protected static $kernel;

    /**
     * @var ContainerInterface
     *
     * Container
     */
    protected static $container;

    /**
     * @var bool
     *
     * Debug mode
     */
    protected static $debug = false;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @throws RuntimeException unable to start the application
     */
    public static function setUpBeforeClass()
    {
        try {
            static::$kernel = static::getKernel();
            static::$kernel->boot();
            static::$application = new Application(static::$kernel);
            static::$application->setAutoExit(false);
            static::$container = static::$kernel->getContainer();
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf('Unable to start the application: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        static::createSchema();
    }

    /**
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $server = []) : Client
    {
        $client = static::$container->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Tear down after class.
     *
     * @throws Exception When doRun returns Exception
     */
    public static function tearDownAfterClass()
    {
        if (!static::loadSchema()) {
            return;
        }

        if (static::$application) {
            static::$application->run(new ArrayInput(
                self::addDebugInConfiguration([
                    'command' => 'doctrine:database:drop',
                    '--no-interaction' => true,
                    '--force' => true,
                ])
            ));
        }
    }

    /**
     * Load fixtures of these bundles.
     *
     * @return array
     */
    protected static function loadFixturePaths() : array
    {
        return [];
    }

    /**
     * Reset database.
     *
     * Performs a completed database reset
     */
    protected function resetDatabase()
    {
        static::tearDownAfterClass();
        static::createSchema();
    }

    /**
     * Has fixtures to load.
     *
     * @return bool
     */
    private static function hasFixturePaths() : bool
    {
        $fixturesBundles = static::loadFixturePaths();

        return
            is_array($fixturesBundles) &&
            !empty($fixturesBundles);
    }

    /**
     * Schema must be loaded in all test cases.
     *
     * @return bool Load schema
     */
    protected static function loadSchema()
    {
        return static::hasFixturePaths();
    }

    /**
     * Creates schema.
     *
     * Only creates schema if loadSchema() is set to true.
     * All other methods will be loaded if this one is loaded.
     */
    protected static function createSchema()
    {
        if (!static::loadSchema()) {
            return;
        }

        static::$application->run(new ArrayInput(
            self::addDebugInConfiguration([
                'command' => 'doctrine:database:drop',
                '--no-interaction' => true,
                '--force' => true,
            ])
        ));

        static::$application->run(new ArrayInput(
            self::addDebugInConfiguration([
                'command' => 'doctrine:database:create',
                '--no-interaction' => true,
            ])
        ));

        static::$application->run(new ArrayInput(
            self::addDebugInConfiguration([
                'command' => 'doctrine:schema:create',
                '--no-interaction' => true,
            ])
        ));

        static::loadFixtures();
    }

    /**
     * load fixtures method.
     *
     * This method is only called if create Schema is set to true
     *
     * Only load fixtures if loadFixtures() is set to true.
     * All other methods will be loaded if this one is loaded.
     *
     * Otherwise, will return.
     */
    protected static function loadFixtures()
    {
        if (!static::hasFixturePaths()) {
            return;
        }

        $fixturePaths = static::loadFixturePaths();

        if (!empty($fixturePaths)) {
            $formattedPaths = array_map(function ($path) {
                return static::$kernel->locateResource($path);
            }, $fixturePaths);

            static::$application->run(new ArrayInput(
                self::addDebugInConfiguration([
                    'command' => 'doctrine:fixtures:load',
                    '--no-interaction' => true,
                    '--fixtures' => $formattedPaths,
                ])
            ));
        }

        return;
    }

    /**
     * Get container service.
     *
     * @param string $serviceName
     *
     * @return mixed The associated service
     */
    public function get(string $serviceName)
    {
        return self::$container->get($serviceName);
    }

    /**
     * Container has service.
     *
     * @param string $serviceName
     *
     * @return bool The container has the service
     */
    public function has(string $serviceName) : bool
    {
        return self::$container->has($serviceName);
    }

    /**
     * Get container parameter.
     *
     * @param string $parameterName
     *
     * @return string
     */
    public function getParameter(string $parameterName)
    {
        return self::$container->getParameter($parameterName);
    }

    /**
     * Get object repository given an entity namespace.
     *
     * @param string $entityNamespace
     *
     * @return ObjectRepository|null
     */
    protected function getObjectRepository(string $entityNamespace) : ? ObjectRepository
    {
        return $this
            ->get('base.object_repository_provider')
            ->getObjectRepositoryByEntityNamespace(
                $this->locateEntity($entityNamespace)
            );
    }

    /**
     * Get object manager given an entity namespace.
     *
     * @param string $entityNamespace
     *
     * @return ObjectManager|null
     */
    protected function getObjectManager(string $entityNamespace) : ? ObjectManager
    {
        return $this
            ->get('base.object_manager_provider')
            ->getObjectManagerByEntityNamespace(
                $this->locateEntity($entityNamespace)
            );
    }

    /**
     * Get the entity instance with id $id.
     *
     * @param string $entityNamespace
     * @param mixed  $id
     *
     * @return object
     */
    public function find(
        string $entityNamespace,
        $id
    ) {
        return $this
            ->getObjectRepository($this->locateEntity($entityNamespace))
            ->find($id);
    }

    /**
     * Get all entity instances.
     *
     * @param string $entityNamespace
     *
     * @return array
     */
    public function findAll($entityNamespace)
    {
        return $this
            ->getObjectRepository($this->locateEntity($entityNamespace))
            ->findAll();
    }

    /**
     * Clear the object manager tracking of an entity.
     *
     * @param string $entityNamespace
     */
    public function clear($entityNamespace)
    {
        $entityNamespace = $this->locateEntity($entityNamespace);
        $this
            ->getObjectManager($entityNamespace)
            ->clear($entityNamespace);
    }

    /**
     * Save entity or array of entities.
     *
     * @param mixed $entities
     */
    protected function save($entities)
    {
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        foreach ($entities as $entity) {
            $entityClass = get_class($entity);
            $entityManager = $this
                ->get('base.object_manager_provider')
                ->getObjectManagerByEntityNamespace($entityClass);
            $entityManager->persist($entity);
            $entityManager->flush($entity);
        }
    }

    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel() : KernelInterface
    {
        throw new RuntimeException('You must implement your own getKernel method');
    }

    /**
     * Add debug line in array if is defined.
     *
     * @param array $configuration
     *
     * @return array
     */
    private static function addDebugInConfiguration(array $configuration) : array
    {
        if (!self::$debug) {
            $configuration['--quiet'] = true;
        }

        return $configuration;
    }

    /**
     * Get entity locator given a string.
     *
     * Available formats:
     *
     * MyBundle\Entity\Namespace\User - Namespace
     * MyBundle:User - Doctrine short alias
     * ~my_prefix:user~ - When using short DoctrineExtraMapping, ~prefix:name~
     * ~my_prefix.entity.user.class~ - When using DoctrineExtraMapping class param
     *
     * @param string $entityAlias
     *
     * @return string
     */
    private function locateEntity($entityAlias)
    {
        if (1 === preg_match('/^~.*?\\.entity\\..*?\\.class~$/', $entityAlias)) {
            return $this->getParameter(trim($entityAlias, '~'));
        }

        if (1 === preg_match('/^~[^:]+:[^:]+~$/', $entityAlias)) {
            return $this->getParameter(
                str_replace(':', '.entity.', trim($entityAlias, '~') . '.class')
            );
        }

        return $entityAlias;
    }
}
