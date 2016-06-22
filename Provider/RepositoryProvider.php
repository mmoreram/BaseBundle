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

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class RepositoryProvider.
 */
class RepositoryProvider
{
    /**
     * @var EntityManagerProvider
     *
     * Manager
     */
    private $managerProvider;

    /**
     * @var ParameterBagInterface
     *
     * Parameter bag
     */
    private $parameterBag;

    /**
     * Construct method.
     *
     * @param EntityManagerProvider $managerProvider Manager
     * @param ParameterBagInterface $parameterBag    Parameter bag
     */
    public function __construct(
        EntityManagerProvider $managerProvider,
        ParameterBagInterface $parameterBag
    ) {
        $this->managerProvider = $managerProvider;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Given an entity namespace, return associated repository.
     *
     * @param string $entityNamespace Entity Namespace
     *
     * @return ObjectRepository Repository
     */
    public function getRepositoryByEntityNamespace($entityNamespace)
    {
        return $this
            ->managerProvider
            ->getEntityManagerByEntityNamespace($entityNamespace)
            ->getRepository($entityNamespace);
    }

    /**
     * Given an entity parameter definition, returns associated repository.
     *
     * This method is only useful when your entities namespaces are defined as
     * a parameter, very useful when you want to provide a way of overriding
     * entities easily
     *
     * @param string $entityParameter Entity Parameter
     *
     * @return ObjectRepository Repository
     */
    public function getRepositoryByEntityParameter($entityParameter)
    {
        $entityNamespace = $this
            ->parameterBag
            ->get($entityParameter);

        return $this->getRepositoryByEntityNamespace($entityNamespace);
    }
}
