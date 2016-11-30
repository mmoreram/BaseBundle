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

namespace Mmoreram\BaseBundle\Tests\Bundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Mmoreram\BaseBundle\Tests\Bundle\Entity\User;

/**
 * Class UserData.
 */
class UserData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Joan');
        $manager->persist($user);

        $user = new User();
        $user->setName('Maria');
        $manager->persist($user);

        $user = new User();
        $user->setName('Pere');
        $manager->persist($user);
        $manager->flush();
    }
}
