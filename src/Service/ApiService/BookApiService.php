<?php

namespace App\Service\ApiService;

use App\Infrastructure\Curl\CurlService;
use App\Infrastructure\Curl\Strategy\CurlGetStrategy;

class BookApiService
{

    public function __construct(private CurlService $curlService)
    {
    }

    public function searchBooksByTitle(string $title): array
    {
        $apiKey = getenv('BOOKS_APP_API_KEY');
        $url = "https://www.googleapis.com/books/v1/volumes?q=?+intitle:$title&key=$apiKey";
        $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

        $data = json_decode($result, true);
        return $data;
    }

    public function searchBooksByCategory(string $category) : array {
        $apiKey = getenv('BOOKS_APP_API_KEY');
        $url = "https://www.googleapis.com/books/v1/volumes?q=?+subject:$category&key=$apiKey";
        $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

        $data = json_decode($result, true);
        return $data;
    }

    public function searchBooksByAuthor(string $author) : array {
        $apiKey = getenv('BOOKS_APP_API_KEY');
        $url = "https://www.googleapis.com/books/v1/volumes?q=?+inauthor:$author&key=$apiKey";
        $result = $this->curlService->setStrategy(new CurlGetStrategy())->setUrl($url)->doRequest();

        $data = json_decode($result, true);
        return $data;
    }
}
