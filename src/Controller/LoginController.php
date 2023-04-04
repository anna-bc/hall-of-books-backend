<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LoginController extends AbstractController {
    #[Route(path: '/login', name: 'api_login')]
    public function index(#[CurrentUser] ?User $user) : Response {
        if ($user === null) {
            return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json(['message' => 'Welcome ' . $user->getUserIdentifier() .  '!', 'user' => $user]);
    }
}