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

namespace Mmoreram\BaseBundle\Provider;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class EntityManagerProvider.
 */
class EntityManagerProvider
{
    /**
     * @var AbstractManagerRegistry
     *
     * Manager
     */
    private $manager;

    /**
     * @var ParameterBag
     *
     * Parameter bag
     */
    private $parameterBag;

    /**
     * Construct method.
     *
     * @param AbstractManagerRegistry $manager      Manager
     * @param ParameterBag            $parameterBag Parameter bag
     */
    public function __construct(
        AbstractManagerRegistry $manager,
        ParameterBag $parameterBag
    ) {
        $this->manager = $manager;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Given an entity namespace, return associated object Manager.
     *
     * @param string $entityNamespace Entity Namespace
     *
     * @return ObjectManager|null Object manager
     */
    public function getEntityManagerByEntityNamespace($entityNamespace)
    {
        return $this
            ->manager
            ->getManagerForClass($entityNamespace);
    }

    /**
     * Given an entity parameter definition, returns associated object Manager.
     *
     * This method is only useful when your entities namespaces are defined as
     * a parameter, very useful when you want to provide a way of overriding
     * entities easily
     *
     * @param string $entityParameter Entity Parameter
     *
     * @return ObjectManager|null Object manager
     */
    public function getEntityManagerByEntityParameter($entityParameter)
    {
        $entityNamespace = $this
            ->parameterBag
            ->get($entityParameter);

        return $this->getEntityManagerByEntityNamespace($entityNamespace);
    }
}
