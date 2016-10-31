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

use Exception;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Symfony\Component\Console\Application;
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
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {
            static::$kernel = $this->getKernel();
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
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $server = [])
    {
        $client = static::$container->get('test.client');
        $client->setServerParameters($server);

        return $client;
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
    public function getParameter(string $parameterName) : string
    {
        return self::$container->getParameter($parameterName);
    }

    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    abstract protected function getKernel();
}
