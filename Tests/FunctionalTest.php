<?php

/*
 * PHP Class Header
 */

namespace Mmoreram\BaseBundle\Tests;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Console\Application;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AbstractTest
 */
abstract class FunctionalTest extends PHPUnit_Framework_TestCase
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
     * kernel
     */
    protected static $kernel;

    /**
     * @var ContainerInterface
     *
     * Container
     */
    protected static $container;

    /**
     * Reload scenario.
     *
     * @throws RuntimeException unable to start the application
     */
    protected function reloadScenario()
    {
        static::setUpBeforeClass();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {
            static::$kernel = new AppKernel('Test', false);
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
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (static::$application) {
            static::$application->run(new ArrayInput([
                'command' => 'doctrine:database:drop',
                '--no-interaction' => true,
                '--force' => true,
                '--quiet' => true,
            ]));
        }
    }

    /**
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $server = [])
    {
        $client = static::$kernel
            ->getContainer()
            ->get('test.client');

        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Get container service.
     *
     * @param string $serviceName Container service name
     *
     * @return mixed The associated service
     */
    public function get($serviceName)
    {
        return self::$container->get($serviceName);
    }
}