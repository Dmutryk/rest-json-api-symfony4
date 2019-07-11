<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\JwtUserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/countries")
 */
class CountryController
{
    private $jwtUser;

    public function __construct(JwtUserInterface $jwtUser)
    {
        $this->jwtUser = $jwtUser;
    }

    /**
     * @Route("", methods="GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAll(): Response
    {
        return new Response($this->getUserData());
    }

    /**
     * @Route("", methods="POST")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function createOne(): Response
    {
        return new Response($this->getUserData());
    }

    private function getUserData(): string
    {
        return json_encode([
            'user_id' => $this->jwtUser->get()->getId(),
            'user_roles' => $this->jwtUser->get()->getRoles(),
        ]);
    }
}