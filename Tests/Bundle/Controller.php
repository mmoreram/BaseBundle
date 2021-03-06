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

namespace Mmoreram\BaseBundle\Tests\Bundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller.
 */
class Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function name(Request $request)
    {
        return new Response($request->query->get('name'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function age(Request $request)
    {
        return new Response($request->query->get('age'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function address(Request $request)
    {
        return new Response($request->query->get('address'));
    }
}
