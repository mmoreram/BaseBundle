<?php

namespace Mmoreram\BaseBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Mmoreram\BaseBundle\DependencyInjection\BaseContainerAccessor;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseFixture.
 */
abstract class BaseFixture extends AbstractFixture implements ContainerAwareInterface
{
    use BaseContainerAccessor;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }
}
