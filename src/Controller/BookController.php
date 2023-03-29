<?php

namespace App\Controller;

use App\Infrastructure\Curl\CurlService;
use App\Infrastructure\Curl\Strategy\CurlGetStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function getenv;


class BookController extends AbstractController
{

  public function __construct(
    private CurlService $curlService,
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
    $apiKey = getenv('BOOKS_APP_API_KEY');
    $url = "https://www.googleapis.com/books/v1/volumes?q=?+intitle:$title&key=$apiKey";
    $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

    return $this->json(['result' => json_decode($result)]);
  }

  public function searchBooksByCategory(string $category): Response
  {
    $apiKey = getenv('BOOKS_APP_API_KEY');
    $url = "https://www.googleapis.com/books/v1/volumes?q=?+subject:$category&key=$apiKey";
    $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

    return $this->json(['result' => json_decode($result)]);
  }

  public function searchBooksByAuthor(string $author): Response
  {
    $apiKey = getenv('BOOKS_APP_API_KEY');
    $url = "https://www.googleapis.com/books/v1/volumes?q=?+inauthor:$author&key=$apiKey";
    $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

    return $this->json(['result' => json_decode($result)]);
  }
}


