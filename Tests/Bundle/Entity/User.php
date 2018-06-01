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
 * File header placeholder.
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * Id
     */
    protected $id;

    /**
     * @var string
     *
     * Name
     */
    protected $name;

    /**
     * Get Id.
     *
     * @return int|null
     */
    public function getId(): ? int
    {
        return $this->id;
    }

    /**
     * Get Name.
     *
     * @return string|null
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set Name.
     *
     * @param string|null $name
     */
    public function setName(? string $name)
    {
        $this->name = $name;
    }
}
