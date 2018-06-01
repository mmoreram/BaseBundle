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

namespace Mmoreram\BaseBundle\DependencyInjection;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait BaseContainerAccessor.
 */
trait BaseContainerAccessor
{
    /**
     * @var ContainerInterface
     *
     * Container
     */
    protected static $container;

    /**
     * Get container service.
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    public function get(string $serviceName)
    {
        return self::$container->get($serviceName);
    }

    /**
     * Container has service.
     *
     * @param string $serviceName
     *
     * @return bool
     */
    public function has(string $serviceName): bool
    {
        return self::$container->has($serviceName);
    }

    /**
     * Get container parameter.
     *
     * @param string $parameterName
     *
     * @return mixed
     */
    public function getParameter(string $parameterName)
    {
        return self::$container->getParameter($parameterName);
    }

    /**
     * Get object repository given an entity namespace.
     *
     * @param string $entityNamespace
     *
     * @return ObjectRepository|null
     */
    protected function getObjectRepository(string $entityNamespace): ? ObjectRepository
    {
        return $this
            ->get('base.object_repository_provider')
            ->getObjectRepositoryByEntityNamespace(
                $this->locateEntity($entityNamespace)
            );
    }

    /**
     * Get object manager given an entity namespace.
     *
     * @param string $entityNamespace
     *
     * @return ObjectManager|null
     */
    protected function getObjectManager(string $entityNamespace): ? ObjectManager
    {
        return $this
            ->get('base.object_manager_provider')
            ->getObjectManagerByEntityNamespace(
                $this->locateEntity($entityNamespace)
            );
    }

    /**
     * Get the entity instance with id $id.
     *
     * @param string $entityNamespace
     * @param mixed  $id
     *
     * @return object
     */
    public function find(
        string $entityNamespace,
        $id
    ) {
        return $this
            ->getObjectRepository($this->locateEntity($entityNamespace))
            ->find($id);
    }

    /**
     * Get the entity instance with criteria.
     *
     * @param string $entityNamespace
     * @param array  $criteria
     *
     * @return object
     */
    public function findOneBy(
        string $entityNamespace,
        array $criteria
    ) {
        return $this
            ->getObjectRepository($this->locateEntity($entityNamespace))
            ->findOneBy($criteria);
    }

    /**
     * Get all entity instances.
     *
     * @param string $entityNamespace
     *
     * @return array
     */
    public function findAll($entityNamespace): array
    {
        return $this
            ->getObjectRepository($this->locateEntity($entityNamespace))
            ->findAll();
    }

    /**
     * Get all entity instances.
     *
     * @param string $entityNamespace
     * @param array  $criteria
     *
     * @return array
     */
    public function findBy(
        string $entityNamespace,
        array $criteria
    ): array {
        return $this
            ->getObjectRepository($this->locateEntity($entityNamespace))
            ->findBy($criteria);
    }

    /**
     * Clear the object manager tracking of an entity.
     *
     * @param string $entityNamespace
     */
    public function clear(string $entityNamespace)
    {
        $entityNamespace = $this->locateEntity($entityNamespace);
        $this
            ->getObjectManager($entityNamespace)
            ->clear($entityNamespace);
    }

    /**
     * Save entity or array of entities.
     *
     * @param mixed $entities
     */
    protected function save($entities)
    {
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        foreach ($entities as $entity) {
            $entityClass = get_class($entity);
            $entityManager = $this
                ->get('base.object_manager_provider')
                ->getObjectManagerByEntityNamespace($entityClass);
            $entityManager->persist($entity);
            $entityManager->flush($entity);
        }
    }

    /**
     * Get entity locator given a string.
     *
     * Available formats:
     *
     * MyBundle\Entity\Namespace\User - Namespace
     * MyBundle:User - Doctrine short alias
     * my_prefix:user - When using short DoctrineExtraMapping, prefix:name
     * my_prefix.entity.user.class - When using DoctrineExtraMapping class param
     *
     * @param string $entityAlias
     *
     * @return string
     */
    private function locateEntity($entityAlias)
    {
        if (1 === preg_match('/^.*?\\.entity\\..*?\\.class$/', $entityAlias)) {
            if (self::$container->hasParameter($entityAlias)) {
                return $this->getParameter($entityAlias);
            }
        }

        if (1 === preg_match('/^[^:]+:[^:]+$/', $entityAlias)) {
            $possibleEntityAliasShortMapping = str_replace(':', '.entity.', $entityAlias.'.class');
            if (self::$container->hasParameter($possibleEntityAliasShortMapping)) {
                return $this->getParameter($possibleEntityAliasShortMapping);
            }
        }

        return $entityAlias;
    }
}
