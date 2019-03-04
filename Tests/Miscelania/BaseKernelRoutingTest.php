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

namespace Mmoreram\BaseBundle\Tests\Miscelania;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\BaseBundle\Tests\BaseFunctionalTest;
use Mmoreram\BaseBundle\Tests\BaseKernel;
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
            SensioFrameworkExtraBundle::class,
        ], [
            'imports' => [
                ['resource' => '@BaseBundle/Resources/test/framework.test.yml'],
            ],
        ], [
            '@TestBaseBundle/Resources/config/routing.yml',
            ['/small', 'TestBaseBundle:Default:small', 'small_route'],
        ]);
    }

    /**
     * Test routes loading.
     */
    public function testRoutes()
    {
        AnnotationRegistry::registerFile(self::$kernel
            ->locateResource('@SensioFrameworkExtraBundle/Configuration/Route.php')
        );

        $client = self::createClient();
        $router = $this->get('router');
        $routeCollection = $router->getRouteCollection();
        $client->request(
            'GET',
            $routeCollection
                ->get('small_annotatted')
                ->getPath()
        );
        $client->request(
            'GET',
            '/small'
        );
        $this->assertTrue(true);
    }
}
