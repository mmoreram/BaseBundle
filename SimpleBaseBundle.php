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

namespace Mmoreram\BaseBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Mmoreram\BaseBundle\DependencyInjection\SimpleBaseExtension;

/**
 * Class AbstractBundle.
 */
abstract class SimpleBaseBundle extends BaseBundle
{
    /**
     * get config files.
     *
     * @return array
     */
    public function getConfigFiles(): array
    {
        return [];
    }

    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        return new SimpleBaseExtension(
            $this,
            $this->getConfigFiles()
        );
    }
}
