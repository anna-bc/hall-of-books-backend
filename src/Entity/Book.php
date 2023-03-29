<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
  #[ORM\Id]
  #[ORM\Column]
  private ?string $id = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[ORM\Column(length: 255)]
  private ?string $publisher = null;

  #[ORM\Column(length: 10, nullable: true)]
  private ?string $publishedDate = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(length: 255)]
  private ?array $industryIdentifiers = [];

  #[ORM\Column(nullable: true)]
  private ?int $pageCount = null;

  #[ORM\Column(nullable: true)]
  private ?float $averageRating = null;

  #[ORM\Column(nullable: true)]
  private ?int $ratingsCount = null;

  #[ORM\Column(length: 20, nullable: true)]
  private ?string $maturityRating = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $thumbnailUrl = null;

  #[ORM\Column(length: 5)]
  private ?string $languageCode = null;

  #[ORM\Column]
  private ?int $quantity = null;

  #[ORM\Column]
  private ?int $numAvailable = null;

  #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'booksInCategory')]
  private Collection $categories;

  #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]

  private Collection $authors;


  public function __construct()
  {
    $this->categories = new ArrayCollection();
    $this->authors = new ArrayCollection();
  }

  public function getId(): ?string
  {
    return $this->id;
  }

  public function setId(string $id): self
  {
    $this->id = $id;

    return $this;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  public function getPublisher(): ?string
  {
    return $this->publisher;
  }

  public function setPublisher(string $publisher): self
  {
    $this->publisher = $publisher;

    return $this;
  }

  public function getPublishedDate(): ?string
  {
    return $this->publishedDate;
  }

  public function setPublishedDate(?string $publishedDate): self
  {
    $this->publishedDate = $publishedDate;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getIndustryIdentifiers(): ?array
  {
    return $this->industryIdentifiers;
  }

  public function setIndustryIdentifiers(array $industryIdentifiers): self
  {
    $this->industryIdentifiers = $industryIdentifiers;

    return $this;
  }

  public function getPageCount(): ?int
  {
    return $this->pageCount;
  }

  public function setPageCount(?int $pageCount): self
  {
    $this->pageCount = $pageCount;

    return $this;
  }

  public function getAverageRating(): ?float
  {
    return $this->averageRating;
  }

  public function setAverageRating(?float $averageRating): self
  {
    $this->averageRating = $averageRating;

    return $this;
  }

  public function getRatingsCount(): ?int
  {
    return $this->ratingsCount;
  }

  public function setRatingsCount(?int $ratingsCount): self
  {
    $this->ratingsCount = $ratingsCount;

    return $this;
  }

  public function getMaturityRating(): ?string
  {
    return $this->maturityRating;
  }

  public function setMaturityRating(?string $maturityRating): self
  {
    $this->maturityRating = $maturityRating;

    return $this;
  }

  public function getThumbnailUrl(): ?string
  {
    return $this->thumbnailUrl;
  }

  public function setThumbnailUrl(?string $thumbnailUrl): self
  {
    $this->thumbnailUrl = $thumbnailUrl;

    return $this;
  }

  public function getLanguageCode(): ?string
  {
    return $this->languageCode;
  }

  public function setLanguageCode(string $languageCode): self
  {
    $this->languageCode = $languageCode;

    return $this;
  }

  public function getQuantity(): ?int
  {
    return $this->quantity;
  }

  public function setQuantity(int $quantity): self
  {
    $this->quantity = $quantity;

    return $this;
  }

  public function getNumAvailable(): ?int
  {
    return $this->numAvailable;
  }

  public function setNumAvailable(int $numAvailable): self
  {
    $this->numAvailable = $numAvailable;

    return $this;
  }

  /**
   * @return Collection<int, Category>
   */
  public function getCategories(): Collection
  {
    return $this->categories;
  }

  public function addCategory(Category $category): self
  {
    if (!$this->categories->contains($category)) {
      $this->categories->add($category);
    }

    return $this;
  }

  public function removeCategory(Category $category): self
  {
    $this->categories->removeElement($category);

    return $this;
  }

  /**
   * @return Collection<int, Author>
   */
  public function getAuthors(): Collection
  {
    return $this->authors;
  }

  public function addAuthor(Author $author): self
  {
    if (!$this->authors->contains($author)) {
      $this->authors->add($author);
    }

    return $this;
  }

  public function removeAuthor(Author $author): self
  {
    $this->authors->removeElement($author);

    return $this;
  }
}
