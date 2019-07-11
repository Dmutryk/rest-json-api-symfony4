<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function home(): Response
    {
        return new Response('Welcome to the API application.');
    }
}