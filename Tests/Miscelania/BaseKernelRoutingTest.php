<?php

/*
 * This file is part of the BaseBundle for Symfony.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Kernel\BaseKernel;
use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\Bundle\Controller;
use Mmoreram\BaseBundle\Tests\Bundle\TestBundle;

/**
 * Class BaseKernelRoutingTest.
 */
class BaseKernelRoutingTest extends BaseFunctionalTest
{
    /**
     * Get kernel.
     *
     * @return KernelInterface
     */
    protected static function getKernel(): KernelInterface
    {
        return new BaseKernel([
            TestBundle::class,
        ], [
            'parameters' => [
                'kernel.secret' => '1234',
            ],
            'framework' => [
                'test' => true,
            ],
        ], [
            '@TestBundle/Resources/config/routing.yml',
            ['/age', Controller::class.'::age', 'age'],
        ]);
    }

    /**
     * Test routes loading.
     */
    public function testRoutes()
    {
        $client = self::createClient();
        $router = $this->get('router');
        $routeCollection = $router->getRouteCollection();
        $client->request(
            'GET',
            $routeCollection
                ->get('name')
                ->getPath(),
            ['name' => 'marc']
        );

        $this->assertEquals('marc', $client
            ->getResponse()
            ->getContent()
        );

        $client->request(
            'GET',
            $routeCollection
                ->get('age')
                ->getPath(),
            ['age' => '12']
        );

        $this->assertEquals('12', $client
            ->getResponse()
            ->getContent()
        );
    }
}
