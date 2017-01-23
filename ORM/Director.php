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

namespace Mmoreram\BaseBundle\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class Director.
 */
class Director
{
    /**
     * @var ObjectManager
     *
     * Object manager
     */
    private $objectManager;

    /**
     * @var ObjectRepository
     *
     * Object repository
     */
    private $objectRepository;

    /**
     * Director constructor.
     *
     * @param ObjectManager    $objectManager
     * @param ObjectRepository $objectRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        ObjectRepository $objectRepository
    ) {
        $this->objectManager = $objectManager;
        $this->objectRepository = $objectRepository;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id
     *
     * @return object
     */
    public function find($id)
    {
        return $this
            ->objectRepository
            ->find($id);
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria
     *
     * @return object
     */
    public function findOneBy(array $criteria)
    {
        return $this
            ->objectRepository
            ->findOneBy($criteria);
    }

    /**
     * Save an entity.
     *
     * @param object $entity
     */
    public function save($entity)
    {
        $this
            ->objectManager
            ->persist($entity);

        $this
            ->objectManager
            ->flush($entity);
    }

    /**
     * Removes an object instance.
     *
     * A removed object will be removed from the database as a result of the flush operation.
     *
     * @param object $object
     */
    public function remove($object)
    {
        $this
            ->objectManager
            ->remove($object);

        $this
            ->objectManager
            ->flush($object);
    }
}
