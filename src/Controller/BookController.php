<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use App\Infrastructure\Curl\CurlService;
use App\Infrastructure\Curl\Strategy\CurlGetStrategy;
use App\Service\ApiService\BookApiService;
use App\Service\DatabaseService\BookDBService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use function getenv;


class BookController extends AbstractController
{

  public function __construct(
    private CurlService $curlService,
    private EntityManagerInterface $entityManager,
    private BookDBService $bookDBService,
    private BookApiService $bookApiService,
  ) {
  }

  public function getBookById(string $id) : Response {
    $book = $this->bookDBService->searchBookById($id);
    if (!$book) {
      return $this->json(['totalItems' => 0, 'data' => 'No Book Found']);
    }

    return $this->json(['totalItems' => 1, 'data' => $book]);
  }

  public function getBooksByTitle(string $title): Response
  {
    $books = $this->bookDBService->searchBooksByTitle($title);

    if ($books) {
      // Book found in the database, return the book information
      return $this->json(
        ['totalItems' => count($books), 'data' => $books],
        Response::HTTP_OK,
        [],
        [
          ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
            return $obj->getId();
          }
        ]
      );
    }
    // if no books can be found in the DB, search for these books in the api
    $result = $this->bookApiService->searchBooksByTitle($title);

    $books = [];
    foreach ($result['items'] ?? [] as $bookData) {
      //check first if a book with the id is already stored in our database
      if ($this->entityManager->getRepository(Book::class)->find($bookData['id'])) {
        continue;
      }

      // Save the book in the database
      $book = $this->bookDBService->saveBookFromApi($bookData);
      $books[] = $book;
    }

    return $this->json(
      ['totalItems' => count($books), 'data' => $books],
      Response::HTTP_OK,
      [],
      [
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
          return $obj->getId();
        }
      ]
    );
  }

  public function getBooksByCategory(string $category): Response
  {
    $books = $this->bookDBService->searchBookByCategory($category);
    if ($books) {
      return $this->json(['totalItems' => count($books), 'data' => $books]);
    }

    $result = $this->bookApiService->searchBooksByCategory($category);
    $books = [];
    foreach ($result['items'] ?? [] as $bookData) {
      //check first if a book with the id is already stored in our database
      if ($this->entityManager->getRepository(Book::class)->find($bookData['id'])) {
        continue;
      }

      // Save the book in the database
      $book = $this->bookDBService->saveBookFromApi($bookData);
      $books[] = $book;
    }

    return $this->json(['totalItems' => count($books), 'data' => $books]);
  }

  public function getBooksByAuthor(string $author): Response
  {
    $books = $this->bookDBService->searchBookByAuthor($author);
    if ($books) {
      return $this->json(['totalItems' => count($books), 'data' => $books]);
    }

    $result = $this->bookApiService->searchBooksByAuthor($author);
    $books = [];
    foreach ($result['items'] ?? [] as $bookData) {
      //check first if a book with the id is already stored in our database
      if ($this->entityManager->getRepository(Book::class)->find($bookData['id'])) {
        continue;
      }

      // Save the book in the database
      $book = $this->bookDBService->saveBookFromApi($bookData);
      $books[] = $book;
    }

    return $this->json(['totalItems' => count($books), 'data' => $books]);
  }

  public function getNewestBooks(): Response
  {
    $books = $this->bookDBService->get10NewestBooks();
    if (!$books) {
      return $this->json(['totalItems' => 0, 'data' => 'No Books Found']);
    }

    return $this->json(
      ['totalItems' => count($books), 'data' => $books],
      Response::HTTP_OK,
      [],
      [
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
          return $obj->getId();
        }
      ]
    );
  }
}
