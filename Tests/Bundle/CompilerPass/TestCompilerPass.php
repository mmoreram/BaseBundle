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

namespace Mmoreram\BaseBundle\Tests\Bundle\CompilerPass;

use Mmoreram\BaseBundle\CompilerPass\TagCompilerPass;

/**
 * Class TestCompilerPass.
 */
final class TestCompilerPass extends TagCompilerPass
{
    /**
     * Get collector service name.
     *
     * @return string Collector service name
     */
    public function getCollectorServiceName() : string
    {
        return 'test.collector';
    }

    /**
     * Get collector method name.
     *
     * @return string Collector method name
     */
    public function getCollectorMethodName() : string
    {
        return 'add';
    }

    /**
     * Get tag name.
     *
     * @return string Tag name
     */
    public function getTagName() : string
    {
        return 'test.tag';
    }
}
