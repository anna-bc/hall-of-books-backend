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

  #[ORM\Column(length: 255)]
  private ?string $categoryName = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $categoryDescription = null;


  public function __construct()
  {
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
}
