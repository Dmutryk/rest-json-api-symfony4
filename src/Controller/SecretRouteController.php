<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\JwtUserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secret-route")
 */
class SecretRouteController
{
    /**
     * @var \App\Auth\JwtUserInterface
     */
    private $jwtUser;

    /**
     * SecretRouteController constructor.
     *
     * @param \App\Auth\JwtUserInterface $jwtUser
     */
    public function __construct(JwtUserInterface $jwtUser)
    {
        $this->jwtUser = $jwtUser;
    }

    /**
     * @Route("", methods="GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function userMethod(): Response
    {
        return new Response('This user data: ' . $this->getUserData());
    }

    /**
     * @Route("", methods="POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminMethod(): Response
    {
        return new Response('This admin data: ' . $this->getUserData());
    }

    /**
     * @return string
     */
    private function getUserData(): string
    {
        return json_encode([
            'user_id' => $this->jwtUser->get()->getId(),
            'user_roles' => $this->jwtUser->get()->getRoles(),
        ]);
    }
}