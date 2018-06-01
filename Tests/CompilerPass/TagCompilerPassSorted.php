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

namespace Mmoreram\BaseBundle\Test\CompilerPass;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;

/**
 * Class TagCompilerPassSorted.
 */
final class TagCompilerPassSorted extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel(
            [
                'Mmoreram\BaseBundle\Tests\Bundle\TestBundle',
            ],
            [
                'services' => [
                    'test.collector' => [
                        'class' => 'Mmoreram\BaseBundle\Tests\Bundle\Collector\TestCollector',
                    ],
                    'class0' => [
                        'abstract' => true,
                        'class' => 'Mmoreram\BaseBundle\Tests\Bundle\TestClass',
                    ],
                    'class1' => [
                        'parent' => 'class0',
                        'arguments' => [
                            'c1',
                        ],
                        'tags' => [
                            ['name' => 'test.tag', 'priority' => -3],
                        ],
                    ],
                    'class2' => [
                        'parent' => 'class0',
                        'arguments' => [
                            'c2',
                        ],
                        'tags' => [
                            ['name' => 'test.tag', 'priority' => 10],
                        ],
                    ],
                    'class3' => [
                        'parent' => 'class0',
                        'arguments' => [
                            'c3',
                        ],
                        'tags' => [
                            ['name' => 'test.tag'],
                        ],
                    ],
                ],
            ],
            []
        );
    }

    /**
     * Test compiler pass.
     */
    public function testCompilerPass()
    {
        $collector = $this->get('test.collector');
        $testClasses = $collector->getTestClasses();
        $this->assertEquals(
            'c2',
            $testClasses[0]->getName()
        );
        $this->assertEquals(
            'c3',
            $testClasses[1]->getName()
        );
        $this->assertEquals(
            'c1',
            $testClasses[2]->getName()
        );
    }
}
