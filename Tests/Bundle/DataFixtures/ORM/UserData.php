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

namespace Mmoreram\BaseBundle\Tests\Bundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Mmoreram\BaseBundle\DataFixtures\BaseFixture;
use Mmoreram\BaseBundle\Tests\Bundle\Entity\User;

/**
 * Class UserData.
 */
class UserData extends BaseFixture
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setName('Joan');
        $this->save($user1);

        $user2 = new User();
        $user2->setName('Maria');
        $this->save($user2);

        $user3 = new User();
        $user3->setName('Pere');
        $this->save($user3);
    }
}
