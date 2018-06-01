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

namespace Mmoreram\BaseBundle\Tests\Bundle\Collector;

use Mmoreram\BaseBundle\Tests\Bundle\TestClass;

/**
 * Class TestCollector.
 */
final class TestCollector
{
    /**
     * @var TestClass[]
     *
     * TestClass array
     */
    private $testClasses = [];

    /**
     * Add TestClass instance.
     *
     * @param TestClass $testClass
     */
    public function add(TestClass $testClass)
    {
        $this->testClasses[] = $testClass;
    }

    /**
     * Get TestClasses.
     *
     * @return TestClass[] TestClasses
     */
    public function getTestClasses(): array
    {
        return $this->testClasses;
    }
}
