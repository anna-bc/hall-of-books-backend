<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 30)]
    private ?string $password = null;

    #[ORM\Column]
    private ?array $roles = [];

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

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getUsername()
    {
        return $this->username;
    }

  public function setUsername(string $username): self
  {
    $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        //guarantess every user has at least ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     * @return string the hashed password for this user
     */
    public function getPassword() : string {
        return $this->password;
    }

    public function setPassword(string $password) : self {
        $this->password = $password;
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

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // clear any temporary sensitive data of the user, ex:
        // $this->plainPassword = null;

        return $this;
    }
}
