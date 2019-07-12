<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(): Response
    {
        return new Response('Welcome to the API application.');
    }
}