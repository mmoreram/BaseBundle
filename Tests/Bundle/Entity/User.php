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
     * @var string
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
     * User constructor.
     *
     * @param string $id
     * @param string $name
     */
    public function __construct(
        string $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Get Id.
     *
     * @return string
     */
    public function getId(): string
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
     * @param string $name
     */
    public function changeName(string $name)
    {
        $this->name = $name;
    }
}
