<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function index(): Response
    {
        return $this->json(
            $this->getUser(),
            context: [
                "groups" => [
                    "user:read"
                ]
            ]
        );
    }
}
