<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank(message: 'titleIsRequired')]
    private ?string $title = null;

    #[ORM\Column(length: 4096)]
    #[Assert\NotBlank(message: 'ingredientsIsRequired')]
    private ?string $ingredients = null;

    #[ORM\Column(length: 4096)]
    #[Assert\NotBlank(message: 'directionsIsRequired')]
    private ?string $directions = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(length: 1024, nullable: true)]
    #[Assert\Url(message: 'linkIsNotUrl')]
    private ?string $link = null;

    #[ORM\Column(length: 1024, nullable: true)]
    #[Assert\Url(message: 'linkIsNotUrl')]
    private ?string $site = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank(message: 'nerIsRequired')]
    private ?string $ner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    public static function createFrom(
        User   $user,
        string $title,
        string $ingredients,
        string $directions,
        string $link,
        string $source,
        string $ner,
        string $site,
        string $imageUrl
    ): self
    {
        $recipe = new self();
        $recipe->setUser($user);
        $recipe->setTitle($title);
        $recipe->setIngredients($ingredients);
        $recipe->setDirections($directions);
        $recipe->setLink($link);
        $recipe->setSource($source);
        $recipe->setNer($ner);
        $recipe->setSite($site);
        $recipe->setImageUrl($imageUrl);
        return $recipe;
    }

    public function toMap(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'ingredients' => $this->getIngredients(),
            'directions' => $this->getDirections(),
            'link' => $this->getLink(),
            'source' => $this->getSource(),
            'ner' => $this->getNer(),
            'site' => $this->getSite(),
            'user' => User::getMap($this->getUser()),
            'imageUrl' => $this->getImageUrl(),
        ];
    }

    public function setId(int $id): void
    {
        $environment = getenv('APP_ENV');
        if ($environment === false) {
            $this->id = $id;
        } else {
            throw new \Exception('setId is only for test environment: ' . $environment);
        }
    }

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
