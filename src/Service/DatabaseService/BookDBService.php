<?php

namespace App\Service\DatabaseService;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class BookDBService
{
    private EntityManagerInterface $em;
    private BookRepository $bookRepository;
    public function __construct(EntityManagerInterface $em, BookRepository $bookRepository)
    {
        $this->em = $em;
        $this->bookRepository = $bookRepository;
    }

    public function searchBookById(string $id) : Book {
        return $this->bookRepository->find($id);
    }

    public function searchBooksByTitle(string $title): array
    {
    var_dump($title);
        $books = $this->bookRepository->createQueryBuilder('b')
            ->where('b.title LIKE :title')
            ->setParameter('title', '%' . urldecode($title) . '%')
            ->getQuery()
            ->getResult();

        return $books;
    }

    public function saveBookFromApi($bookData): Book
    {
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
            $category = $this->em->getRepository(Category::class)->findOneBy(['categoryName' => $categoryName]);
            if (!$category) {
                $category = new Category();
                $category->setCategoryName($categoryName);
                $this->em->persist($category);
            }
            $book->addCategory($category);
        }

        // Save authors
        foreach ($bookData['volumeInfo']['authors'] ?? [] as $authorName) {
            $author = $this->em->getRepository(Author::class)->findOneBy(['lastName' => $authorName]);
            if (!$author) {
                $author = new Author();
                $author->setLastName($authorName);
                $this->em->persist($author);
            }
            $book->addAuthor($author);
        }

        $this->em->persist($book);

        $this->em->flush();

        return $book;
    }

    public function get10NewestBooks() : array {
        return $this->bookRepository->findBy(['languageCode' => ['en', 'de']], ['publishedDate' => 'DESC'], 10);
    }
}
