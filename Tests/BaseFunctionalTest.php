<?php

/*
 * This file is part of the BaseBundle for Symfony.
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

use Drift\HttpKernel\AsyncKernel;
use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

/**
 * Class BaseFunctionalTest.
 */
abstract class BaseFunctionalTest extends TestCase
{
    /**
     * @var ContainerInterface
     *
     * Container
     */
    protected static $container;

    /**
     * @var Application
     *
     * application
     */
    protected static $application;

    /**
     * @var KernelInterface|AsyncKernel
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

            if (class_exists('\Symfony\Component\Console\Application')) {
                static::$application = new Application(static::$kernel);
                static::$application->setAutoExit(false);
            }

            static::$container = static::$kernel->getContainer();
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('Unable to start the application: %s', $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     *
     * @return HttpKernelBrowser
     */
    protected static function createClient(array $server = []): HttpKernelBrowser
    {
        $client = static::$container->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Runs a command and returns its output as a string value.
     *
     * @param array $command
     *
     * @return string
     */
    protected static function runCommand(array $command): string
    {
        if (!static::$application instanceof Application) {
            throw new \Exception('You should install the symfony/console component to run commands');
        }

        $fp = tmpfile();
        $input = new ArrayInput($command);
        $output = new StreamOutput($fp);

        static::$application->run(
            $input,
            $output
        );

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = $output.fread($fp, 4096);
        }
        fclose($fp);

        return $output;
    }

    /**
     * Runs a command in async mode and return a Process.
     *
     * @param array          $command
     * @param InputInterface $input
     *
     * @return Process
     */
    protected static function runAsyncCommand(
        array $command,
        InputInterface $input = null
    ): Process {
        if (!static::$application instanceof Application) {
            throw new \Exception('You should install the symfony/console component to run commands');
        }

        $kernel = self::$kernel;
        $jsonSerializedKernel = json_encode($kernel->toArray());
        $jsonSerializedKernelHash = '/kernel'.rand(1, 99999999999999).'.kernel.json';
        $jsonSerializedKernelPath = $kernel->getProjectDir().$jsonSerializedKernelHash;

        file_put_contents(
            $jsonSerializedKernelPath,
            $jsonSerializedKernel
        );

        $devConsolePath = realpath(__DIR__.'/../bin/dev-console');
        array_unshift($command, 'php', $devConsolePath);
        array_push($command, '--kernel-hash-path='.$jsonSerializedKernelPath);

        $process = new Process($command);

        if (!is_null($input)) {
            $process->setInput($input);
        }

        $process->start();

        return $process;
    }

    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        throw new RuntimeException('You must implement your own getKernel method');
    }

    /**
     * Get container service.
     *
     * @param string $serviceName
     *
     * @return mixed
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
     * @return bool
     */
    public function has(string $serviceName): bool
    {
        return self::$container->has($serviceName);
    }

    /**
     * Get container parameter.
     *
     * @param string $parameterName
     *
     * @return mixed
     */
    public function getParameter(string $parameterName)
    {
        return self::$container->getParameter($parameterName);
    }
}
