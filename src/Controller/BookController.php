<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Infrastructure\Curl\CurlService;
use App\Infrastructure\Curl\Strategy\CurlGetStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function getenv;


class BookController extends AbstractController
{

  public function __construct(
    private CurlService $curlService,
    private EntityManagerInterface $entityManager,
  ) {
  }

  #[Route('/books', name: 'app_book')]
  public function index(): Response
  {
    return $this->render('book/index.html.twig', [
      'controller_name' => 'BookController',
    ]);
  }

  public function searchBooksByTitle(string $title): Response
  {
    $bookRepository = $this->entityManager->getRepository(Book::class);
    $book = $bookRepository->findOneBy(['title' => $title]);
    // $books = $bookRepository->createQueryBuilder('b')
    // ->where('b.title LIKE :title')
    // ->setParameter('title', '%' . $title . '%')
    // ->getQuery()
    // ->getResult();

    if ($book) {
      // Book found in the database, return the book information
      return $this->json(['result' => $book]);
    }

    $apiKey = getenv('BOOKS_APP_API_KEY');
    $url = "https://www.googleapis.com/books/v1/volumes?q=?+intitle:$title&key=$apiKey";
    $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

    $data = json_decode($result, true);

    // Save the book in the database
    $bookData = $data['items'][0];
    $book = new Book();
    $book->setId($bookData['id']);
    $book->setTitle($bookData['volumeInfo']['title']);
    $book->setPublisher($bookData['volumeInfo']['publisher'] ?? '');
    $book->setPublishedDate($bookData['volumeInfo']['publishedDate'] ?? '');
    $book->setDescription($bookData['volumeInfo']['description'] ?? '');
    $book->setPageCount($bookData['volumeInfo']['pageCount'] ?? 0);
    $book->setAverageRating($bookData['volumeInfo']['averageRating'] ?? 0);
    $book->setRatingsCount($bookData['volumeInfo']['ratingsCount'] ?? 0);
    $book->setMaturityRating($bookData['volumeInfo']['maturityRating'] ?? '');
    $book->setThumbnailUrl($bookData['volumeInfo']['imageLinks']['thumbnail'] ?? '');
    $book->setLanguageCode($bookData['volumeInfo']['language'] ?? '');
    $book->setQuantity(5);
    $book->setNumAvailable(5);

    // Save categories
    foreach ($bookData['volumeInfo']['categories'] ?? [] as $categoryName) {
      $category = $this->entityManager->getRepository(Category::class)->findOneBy(['categoryName' => $categoryName]);
      if (!$category) {
        $category = new Category();
        $category->setCategoryName($categoryName);
        $this->entityManager->persist($category);
      }
      $book->addCategory($category);
    }

    // Save authors
    foreach ($bookData['volumeInfo']['authors'] ?? [] as $authorName) {
      $author = $this->entityManager->getRepository(Author::class)->findOneBy(['lastName' => $authorName]);
      if (!$author) {
        $author = new Author();
        $author->setLastName($authorName);
        $this->entityManager->persist($author);
      }
      $book->addAuthor($author);
    }

    $this->entityManager->persist($book);

    $this->entityManager->flush();

    return $this->json(['result' => $book]);
  }
}
