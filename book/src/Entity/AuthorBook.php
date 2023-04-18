<?php

namespace App\Entity;

use App\Repository\AuthorBookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorBookRepository::class)]
class AuthorBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $AuthorId = null;

    #[ORM\Column]
    private ?int $BookId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorId(): ?int
    {
        return $this->AuthorId;
    }

    public function setAuthorId(int $AuthorId): self
    {
        $this->AuthorId = $AuthorId;

        return $this;
    }

    public function getBookId(): ?int
    {
        return $this->BookId;
    }

    public function setBookId(int $BookId): self
    {
        $this->BookId = $BookId;

        return $this;
    }
}
