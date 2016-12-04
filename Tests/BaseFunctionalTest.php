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

namespace Mmoreram\BaseBundle\Tests;

use Exception;
use Mmoreram\BaseBundle\DependencyInjection\BaseContainerAccessor;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class BaseFunctionalTest.
 */
abstract class BaseFunctionalTest extends PHPUnit_Framework_TestCase
{
    use BaseContainerAccessor;

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
     * Reset fixtures.
     *
     * Performs a completed fixtures reset
     */
    protected function reloadFixtures()
    {
        static::loadFixtures();
    }

    /**
     * Reset schema.
     */
    protected function reloadSchema()
    {
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
     * @return bool
     */
    protected static function loadSchema() : bool
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

        foreach (self::getManagersName() as $managerName) {
            static::$application->run(new ArrayInput(
                self::addDebugInConfiguration([
                    'command' => 'doctrine:schema:create',
                    '--no-interaction' => true,
                    '--em' => $managerName,
                ])
            ));
        }

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
     * Otherwise, will skip.
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
        if (!static::$debug) {
            $configuration['--quiet'] = true;
        }

        return $configuration;
    }

    /**
     * Get all available entity_managers.
     *
     * @return string[]
     */
    private static function getManagersName()
    {
        return array_keys(self::
            $container
            ->get('doctrine')
            ->getManagers()
        );
    }
}
