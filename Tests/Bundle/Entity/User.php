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

namespace Mmoreram\BaseBundle\Tests\Bundle\Entity;

/**
 * File header placeholder.
 */
class User
{
    /**
     * @var int
     *
     * Id
     */
    private $id;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * Get Id.
     *
     * @return null|int
     */
    public function getId(): ? int
    {
        return $this->id;
    }

    /**
     * Get Name.
     *
     * @return null|mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name.
     *
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
