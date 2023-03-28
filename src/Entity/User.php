<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 20)]
  private ?string $username = null;

  #[ORM\Column(length: 30)]
  private ?string $firstName = null;

  #[ORM\Column(length: 30)]
  private ?string $lastName = null;

  #[ORM\Column(length: 10)]
  private ?string $registrationDate = null;

#[ORM\ManyToMany(targetEntity: Book::class)]
#[ORM\JoinTable(name: 'user_favoriteBooks')]
private Collection $favorites;

#[ORM\ManyToMany(targetEntity: Book::class)]

#[ORM\JoinTable(name: 'user_borrowedBooks')]
private Collection $borrowedBooks;

  public function __construct()
  {
    $this->favorites = new ArrayCollection();
    $this->borrowedBooks = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUsername(): ?string
  {
    return $this->username;
  }

  public function setUsername(string $username): self
  {
    $this->username = $username;

    return $this;
  }

  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): self
  {
    $this->firstName = $firstName;

    return $this;
  }

  public function getLastName(): ?string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): self
  {
    $this->lastName = $lastName;

    return $this;
  }

  public function getRegistrationDate(): ?string
  {
    return $this->registrationDate;
  }

  public function setRegistrationDate(string $registrationDate): self
  {
    $this->registrationDate = $registrationDate;

    return $this;
  }

  /**
   * @return Collection<int, Book>
   */
  public function getFavorites(): Collection
  {
    return $this->favorites;
  }

  public function addFavorite(Book $favorite): self
  {
    if (!$this->favorites->contains($favorite)) {
      $this->favorites->add($favorite);
    }

    return $this;
  }

  public function removeFavorite(Book $favorite): self
  {
    $this->favorites->removeElement($favorite);

    return $this;
  }

  /**
   * @return Collection<int, Book>
   */
  public function getBorrowedBooks(): Collection
  {
    return $this->borrowedBooks;
  }

  public function addBorrowedBook(Book $borrowedBook): self
  {
    if (!$this->borrowedBooks->contains($borrowedBook)) {
      $this->borrowedBooks->add($borrowedBook);
    }

    return $this;
  }

  public function removeBorrowedBook(Book $borrowedBook): self
  {
    $this->borrowedBooks->removeElement($borrowedBook);

    return $this;
  }
}
