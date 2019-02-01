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

namespace Mmoreram\BaseBundle\Tests\Bundle\Entity;

/**
 * Interface UserInterface.
 */
interface UserInterface
{
    /**
     * Get Id.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get Name.
     *
     * @return string|null
     */
    public function getName(): string;

    /**
     * Set Name.
     *
     * @param string $name
     */
    public function changeName(string $name);
}
