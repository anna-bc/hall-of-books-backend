<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\DatabaseService\BookDBService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
  public function __construct(
    private UserRepository $userRepository,
    private EntityManagerInterface $entityManager,
    private BookDBService $bookDBService,
  ) {
  }

  public function displayFavorites(#[CurrentUser] ?User $user): Response
  {
    if ($user === null) {
      return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }
    return $this->json(['favorites' => $user->getFavorites()]);
  }

  public function displayBorrowed(#[CurrentUser] ?User $user): Response
  {
    if ($user === null) {
      return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }
    return $this->json(['borrowed' => $user->getBorrowedBooks()]);
  }

  public function addFavoriteBook(#[CurrentUser] ?User $user, string $id): Response
  {
    if ($user === null) {
      return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }

    $book = $this->bookDBService->searchBookById($id);

    if ($book === null) {
      return $this->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
    }

    if ($user->getFavorites()->contains($book)) {
      return $this->json(['message' => 'Book already in favorites'], Response::HTTP_BAD_REQUEST);
    }

    $user->addFavorite($book);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $this->json(['success' => true, 'data' => $book]);
  }
}
