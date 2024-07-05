<?php

namespace App\Services;

use App\Entity\Recipe;
use App\Entity\User;
use App\Model\SourceChoices;
use App\Model\UiRecipe;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;

class RecipeService
{
    private string $projectDir;
    private string $hostUrl;

    public function __construct(string $projectDir, string $hostUrl)
    {
        $this->projectDir = $projectDir;
        $this->hostUrl = $hostUrl;
    }

    function getUpdatedRecipe(Recipe $recipe, FormInterface $form, User $user): Recipe|Response
    {
        $recipe->setUser($user);
        $recipe->setIngredients($this->stringToJsonArray($recipe->getIngredients()));
        $recipe->setDirections($this->stringToJsonArray($recipe->getDirections()));
        $recipe->setNer($this->stringToJsonArray($recipe->getNer()));

        $imagePath = $form->get('imageUrl')->getData();
        if ($imagePath) {
            $newFileName = uniqid() . '.' . $imagePath->guessExtension();
            try {
                $imagePath->move(
                    $this->projectDir . '/public/recipe/uploads',
                    $newFileName
                );
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }
            $recipe->setImageUrl($this->hostUrl . "/recipe/uploads/" . $newFileName);
        }
        return $recipe;
    }


    function stringToJsonArray($inputString): string
    {
        $normalizedString = str_replace("\r\n", "\n", $inputString);
        $normalizedString = str_replace("\r", "\n", $normalizedString);

        $linesArray = explode("\n", $normalizedString);
        $filteredArray = array_filter($linesArray, function ($line) {
            return trim($line) !== '';
        });
        if (empty($filteredArray)) {
            $filteredArray = [$inputString];
        }
        $filteredArray = array_values($filteredArray);
        return json_encode($filteredArray);
    }
}