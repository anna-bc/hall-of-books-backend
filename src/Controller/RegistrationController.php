<?php

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function index(UserPasswordHasherInterface $passwordHasher, Request $request)
    {
        // get User data from Registration Form
        $postData = $request->request;

        $user = (new User())
            ->setUsername($postData->get('username'))
            ->setRegistrationDate((new DateTime())->format('d-m-Y'))
            ->setFirstName($postData->get('firstName'))
            ->setLastName($postData->get('lastName'))
            ->setRoles(['ROLE_USER']);
        $plaintextPassword = $postData->get('password');

        // hash password with bcrypt
        $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);

        //save user in DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['newUser' => $user]);
    }
}
