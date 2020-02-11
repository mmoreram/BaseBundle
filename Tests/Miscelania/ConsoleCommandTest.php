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

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Mmoreram\BaseBundle\Kernel\BaseKernel;
use Mmoreram\BaseBundle\Tests\Bundle\TestEmptyBundle;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;

/**
 * Class ConsoleCommandTest.
 */
class ConsoleCommandTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            TestEmptyBundle::class
        ], [
            'imports' => [
                ['resource' => '@TestEmptyBundle/Resources/config/services.yml']
            ],
            'parameters' => [
                'kernel.secret' => '1234'
            ],
            'framework' => [
                'test' => true
            ]
        ]);
    }

    /**
     * Test sync command
     */
    public function testSyncConsoleCommand()
    {
        $output = $this->runCommand([
            'test:command'
        ]);

        $this->assertContains('First step', $output);
        $this->assertContains('Second step', $output);
    }

    /**
     * Test async command
     */
    public function testAsyncConsoleCommand()
    {
        $process = $this->runAsyncCommand([
            'test:command'
        ]);

        $this->assertNotContains('First step', $process->getOutput());
        $this->assertNotContains('Second step', $process->getOutput());

        usleep(200000);

        $this->assertContains('First step', $process->getOutput());
        $this->assertNotContains('Second step', $process->getOutput());

        usleep(200000);

        $this->assertContains('First step', $process->getOutput());
        $this->assertContains('Second step', $process->getOutput());
        $process->stop();
    }
}
