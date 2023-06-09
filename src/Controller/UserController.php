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

  public function getUserInfo(#[CurrentUser] ?User $user): Response
  {
    if ($user === null) {
      return $this->json(['message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }

    $userInfo = [
      'id' => $user->getId(),
      'username' => $user->getUsername(),
      'firstName' => $user->getFirstName(),
      'lastName' => $user->getLastName(),
      'registrationDate' => $user->getRegistrationDate(),
    ];

    return $this->json(['user' => $userInfo], Response::HTTP_OK);
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

  public function addFavoriteBook(string $id, #[CurrentUser] ?User $user): Response
  {
    if ($user === null) {
      return $this->json(['success' => false, 'message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }

    $book = $this->bookDBService->searchBookById($id);

    if ($book === null) {
      return $this->json(['success' => false, 'message' => 'Book not found'], Response::HTTP_NOT_FOUND);
    }

    if ($user->getFavorites()->contains($book)) {
      return $this->json(['success' => false, 'message' => 'Book is already in favorites'], Response::HTTP_BAD_REQUEST);
    }

    $user->addFavorite($book);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    $favorites = [];
    foreach ($user->getFavorites() as $pos => $borrowedBook) {
      array_push($favorites, $borrowedBook);
    }

    return $this->json(['success' => true, 'message' => 'Book added to favorites successfully', 'favoriteList' => $favorites, 'book' => $book], Response::HTTP_OK);
  }

  public function removeFavoriteBook(#[CurrentUser] ?User $user, string $id): Response
  {
    if ($user === null) {
      return $this->json(['success' => false, 'message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }

    $book = $this->bookDBService->searchBookById($id);

    if ($book === null) {
      return $this->json(['success' => false, 'message' => 'Book not found'], Response::HTTP_NOT_FOUND);
    }

    if (!$user->getFavorites()->contains($book)) {
      return $this->json(['success' => false, 'message' => 'Book is not in favorites'], Response::HTTP_BAD_REQUEST);
    }

    $user->removeFavorite($book);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    $favorites = [];
    foreach ($user->getFavorites() as $pos => $borrowedBook) {
      array_push($favorites, $borrowedBook);
    }

    return $this->json(['success' => true, 'message' => 'Book removed from favorites successfully', 'favoriteList' => $favorites, 'book' => $book], Response::HTTP_OK);
  }

  public function addBorrowedBook(string $id, #[CurrentUser] ?User $user)
  {
    if (!$user) {
      return $this->json(['success' => false, 'message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }

    $book = $this->bookDBService->searchBookById($id);
    if (!$book) {
      return $this->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
    }
    if ($user->getBorrowedBooks()->contains($book)) {
      return $this->json(['success' => false, 'message' => 'Book is already borrowed by the user'], Response::HTTP_BAD_REQUEST);
    }

    $numAvailable = $book->getNumAvailable();
    if ($numAvailable <= 0) {
      return $this->json(['success' => false, 'message' => 'No Books available to borrow'], Response::HTTP_BAD_REQUEST);
    }

    $user->addBorrowedBook($book);
    $this->entityManager->persist($user);

    $book->setNumAvailable($numAvailable - 1);
    $this->entityManager->persist($book);

    $this->entityManager->flush();

    $borrowedList = [];
    foreach ($user->getBorrowedBooks() as $pos => $borrowedBook) {
      array_push($borrowedList, $borrowedBook);
    }

    return $this->json(['success' => true, 'borrowedList' => $borrowedList, 'book' => $book], Response::HTTP_OK);
  }

  public function returnBorrowedBook(string $id, #[CurrentUser] ?User $user): Response
  {
    if ($user === null) {
      return $this->json(['success' => false, 'message' => 'missing or wrong credentials'], Response::HTTP_UNAUTHORIZED);
    }

    $book = $this->bookDBService->searchBookById($id);

    if ($book === null) {
      return $this->json(['success' => false, 'message' => 'Book not found'], Response::HTTP_NOT_FOUND);
    }

    if (!$user->getBorrowedBooks()->contains($book)) {
      return $this->json(['success' => false, 'message' => 'Book is not borrowed'], Response::HTTP_BAD_REQUEST);
    }

    $user->removeBorrowedBook($book);
    $this->entityManager->persist($user);

    $book->setNumAvailable($book->getNumAvailable() + 1);
    $this->entityManager->persist($book);

    $this->entityManager->flush();

    $borrowedList = [];
    foreach($user->getBorrowedBooks() as $pos => $borrowedBook) {
      array_push($borrowedList, $borrowedBook);
    }

    return $this->json(['success' => true, 'message' => 'Book returned successfully', 'borrowedList' => $borrowedList, 'book' => $book], Response::HTTP_OK);
  }
}
