<?php

namespace App\Services;

use App\Entity\Recipe;
use App\Entity\User;
use App\Model\SourceChoices;
use App\Model\UiRecipe;
use App\Repository\RecipeRepository;
use PHPUnit\Util\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RecipeService
{

    public function __construct()
    {
    }

    public function getUpdatedRecipe(Recipe $recipe, FormInterface $form, User $user, string $projectDir, string $hostUrl): Recipe|Response
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
                    $projectDir . '/public/recipe/uploads',
                    $newFileName
                );
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }
            $recipe->setImageUrl($hostUrl . "/recipe/uploads/" . $newFileName);
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

    public function validateAndConvertRecipe(array $data, ?User $user): string|Recipe
    {
        if (!$user) {
            return 'User not found';
        }

        $source = $data['source'];
        $enumClass = new \ReflectionEnum(SourceChoices::class);

        if (!$enumClass->hasCase($source)) {
            return 'Source have unacceptable value, available values are: '
                . implode(', ', array_keys($enumClass->getConstants()));
        }

        if ($source === SourceChoices::WEB->name &&
            (!$this->isValidUrl($data['link']) || !$this->isValidUrl($data['site']))) {
            return "Link or site is not a valid URL, it is required when source = WEB";
        }

        return Recipe::createFrom(
            $user,
            $data['title'],
            $data['ingredients'],
            $data['directions'],
            $data['link'],
            $data['source'],
            $data['ner'],
            $data['site'],
            $data['imageUrl']
        );
    }

    private function isValidUrl($url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}