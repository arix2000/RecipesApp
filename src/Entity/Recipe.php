<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    private ?string $title = null;

    #[ORM\Column(length: 4096)]
    private ?string $ingredients = null;

    #[ORM\Column(length: 4096)]
    private ?string $directions = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $ner = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $site = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): static
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getDirections(): ?string
    {
        return $this->directions;
    }

    public function setDirections(string $directions): static
    {
        $this->directions = $directions;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getNer(): ?string
    {
        return $this->ner;
    }

    public function setNer(?string $ner): static
    {
        $this->ner = $ner;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
