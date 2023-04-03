<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Infrastructure\Curl\CurlService;
use App\Infrastructure\Curl\Strategy\CurlGetStrategy;
use App\Service\ApiService\BookApiService;
use App\Service\DatabaseService\BookDBService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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


  public function searchBooksByTitle(string $title): Response
  {
    $books = $this->bookDBService->searchBooksByTitle($title);

    if ($books) {
      // Book found in the database, return the book information
      return $this->json(
        ['totalItems' => count($books), 'result' => $books],
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
      ['totalItems' => count($books), 'result' => $books],
      Response::HTTP_OK,
      [],
      [
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj) {
          return $obj->getId();
        }
      ]
    );
  }

  public function searchBooksByCategory(string $category): Response
  {
    $result = $this->bookApiService->searchBooksByCategory($category);

    return $this->json(['totalItems' => $result['totalItems'], 'data' => $result['items']]);
  }

  public function searchBooksByAuthor(string $author): Response
  {
    $result = $this->bookApiService->searchBooksByAuthor($author);

    return $this->json(['totalItems' => $result['totalItems'], 'data' => $result['items']]);
  }

  public function displayNewestBooks(): Response
  {
    $books = $this->bookDBService->get10NewestBooks();
    if (!$books) {
      return $this->json(['totalItems' => 0, 'result' => 'No Books Found']);
    }

    return $this->json(
      ['totalItems' => count($books), 'result' => $books],
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
