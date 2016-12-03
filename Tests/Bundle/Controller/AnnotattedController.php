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

namespace Mmoreram\BaseBundle\Tests\Bundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AnnotattedController.
 */
class AnnotattedController extends Controller
{
    /**
     * Small annotatted action.
     *
     * @Route("/small-annotatted", name="small_annotatted")
     */
    public function smallAnnotattedAction()
    {
        return new Response();
    }
}
