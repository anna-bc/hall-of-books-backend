<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LogoutController extends AbstractController {
    public function __construct(private EntityManagerInterface $em, private AccessTokenRepository $acRepository) {}

    public function logout(#[CurrentUser] ?User $user) {
        if (!$user) {
            return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $tokens = $this->acRepository->findBy(['ownedBy' => $user->getId()]);
        foreach ($tokens as $pos => $token) {
            $this->acRepository->remove($token);
        }
        $this->em->flush();

        return $this->json(['message' => 'Successfully logged out user ' . $user->getUserIdentifier()], Response::HTTP_OK);
    }
}