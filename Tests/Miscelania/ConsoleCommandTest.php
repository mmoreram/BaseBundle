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

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Mmoreram\BaseBundle\Kernel\BaseKernel;
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\Bundle\TestEmptyBundle;
use Symfony\Component\HttpKernel\KernelInterface;

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
            TestEmptyBundle::class,
        ], [
            'imports' => [
                ['resource' => '@TestEmptyBundle/Resources/config/services.yml'],
            ],
            'parameters' => [
                'kernel.secret' => '1234',
            ],
            'framework' => [
                'test' => true,
            ],
        ]);
    }

    /**
     * Test sync command.
     */
    public function testSyncConsoleCommand()
    {
        $output = $this->runCommand([
            'test:command',
        ]);

        $this->assertStringContainsString('First step', $output);
        $this->assertStringContainsString('Second step', $output);
    }

    /**
     * Test async command.
     */
    public function testAsyncConsoleCommand()
    {
        $process = $this->runAsyncCommand([
            'test:command',
        ]);

        $this->assertStringNotContainsString('First step', $process->getOutput());
        $this->assertStringNotContainsString('Second step', $process->getOutput());

        usleep(200000);

        $this->assertStringContainsString('First step', $process->getOutput());
        $this->assertStringNotContainsString('Second step', $process->getOutput());

        usleep(200000);

        $this->assertStringContainsString('First step', $process->getOutput());
        $this->assertStringContainsString('Second step', $process->getOutput());
        $process->stop();
    }
}
