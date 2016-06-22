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

namespace Mmoreram\BaseBundle\EventDispatcher\Abstracts;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractEventDispatcher.
 */
abstract class AbstractEventDispatcher
{
    /**
     * @var EventDispatcherInterface
     *
     * Event dispatcher
     */
    protected $eventDispatcher;

    /**
     * Construct.
     *
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
