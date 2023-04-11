<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class LoginController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/login', name: 'api_login')]
    public function login(#[CurrentUser] ?User $user): Response
    {
        if ($user === null) {
            return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $tokens = $this->em->getRepository(AccessToken::class)->findBy(['ownedBy' => $user->getId()]);
        foreach ($tokens as $pos => $token) {
            var_dump($token->getToken());
            if (!$token->isValid()) {
                continue;
            }
            $validToken = $token;
            break;
        }

        if (!$validToken ) {
            $token = new AccessToken();
            $token->setOwnedBy($user)
                ->setExpiresAt((new \DateTimeImmutable('now'))->modify('+1 week'));

            $this->em->persist($token);
            $this->em->flush();
        }


        return $this->json(['message' => 'Welcome ' . $user->getUserIdentifier() .  '!', 'user' => $user, 'token' => $token->getToken()],
            Response::HTTP_OK,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
                    return $obj->getId();
                }
            ]);
    }
}
