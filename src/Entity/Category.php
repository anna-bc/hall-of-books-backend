<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 30)]
  private ?string $categoryName = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $categoryDescription = null;

  #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'categories')]
  private Collection $booksInCategory;

  public function __construct()
  {
    $this->booksInCategory = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getCategoryName(): ?string
  {
    return $this->categoryName;
  }

  public function setCategoryName(string $categoryName): self
  {
    $this->categoryName = $categoryName;

    return $this;
  }

  public function getCategoryDescription(): ?string
  {
    return $this->categoryDescription;
  }

  public function setCategoryDescription(?string $categoryDescription): self
  {
    $this->categoryDescription = $categoryDescription;

    return $this;
  }

  /**
   * @return Collection<int, Book>
   */
  public function getBooksInCategory(): Collection
  {
    return $this->booksInCategory;
  }

  public function addBooksInCategory(Book $booksInCategory): self
  {
    if (!$this->booksInCategory->contains($booksInCategory)) {
      $this->booksInCategory->add($booksInCategory);
      $booksInCategory->addCategory($this);
    }

    return $this;
  }

  public function removeBooksInCategory(Book $booksInCategory): self
  {
    if ($this->booksInCategory->removeElement($booksInCategory)) {
      $booksInCategory->removeCategory($this);
    }

    return $this;
  }
}
