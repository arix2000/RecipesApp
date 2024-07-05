<?php

namespace App\Model;

use App\Entity\Recipe;
use App\Entity\User;

class UiRecipe
{
    private int $id;

    private string $title;

    private array $ingredients;

    private array $directions;

    private ?string $link;

    private string $source;

    private string $ner;

    private ?string $site;

    private User $user;

    private ?string $imageUrl;

    public function __construct(
        int     $id,
        string  $title,
        array   $ingredients,
        array   $directions,
        ?string $link,
        string  $source,
        string  $ner,
        ?string $site,
        User    $user,
        ?string $imageUrl
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->ingredients = $ingredients;
        $this->directions = $directions;
        $this->link = $link;
        $this->source = $source;
        $this->ner = $ner;
        $this->site = $site;
        $this->user = $user;
        $this->imageUrl = $imageUrl;
    }

    public static function from(Recipe $recipe): UiRecipe
    {
        $imageUrl = $recipe->getImageUrl();
        $isValidUrl = filter_var($imageUrl, FILTER_VALIDATE_URL) !== false;
        $recipe->setImageUrl($isValidUrl ? $imageUrl : null);

        $sourceCaseName = $recipe->getSource();
        $sourceEnum = constant(SourceChoices::class . "::$sourceCaseName");

        return new self(
            $recipe->getId(),
            $recipe->getTitle(),
            json_decode($recipe->getIngredients()),
            json_decode($recipe->getDirections()),
            $recipe->getLink(),
            $sourceEnum->value,
            implode(", ", json_decode($recipe->getNer())),
            $recipe->getSite(),
            $recipe->getUser(),
            $recipe->getImageUrl()
        );
    }

    public function getDirections(): array
    {
        return $this->directions;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getNer(): string
    {
        return $this->ner;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getId(): int
    {
        return $this->id;
    }
}