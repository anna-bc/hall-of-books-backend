<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function displayFavorites(#[CurrentUser] ?User $user): Response
    {
        if ($user === null) {
            return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->userRepository->find($user->getId());
        return $this->json(['favorites' => $user->getFavorites()]);
    }

    public function displayBorrowed(#[CurrentUser] ?User $user): Response
    {
        if ($user === null) {
            return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepository->find($user->getId());
        return $this->json(['borrowed' => $user->getBorrowedBooks()]);
    }
}