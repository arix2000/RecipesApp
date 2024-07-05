<?php

namespace App\Tests\Service;

use App\Entity\Recipe;
use App\Entity\User;
use App\Model\UiRecipe;
use App\Services\RecipeService;
use PHPUnit\Framework\TestCase;

class RecipeServiceTest extends TestCase
{
    public function testStringToJsonArray()
    {
        $projectDir = '/tmp';
        $hostUrl = 'http://localhost';
        $recipeService = new RecipeService($projectDir, $hostUrl);

        $inputString = "line1\nline2\nline3";
        $expectedOutput = json_encode(['line1', 'line2', 'line3']);

        $output = $recipeService->stringToJsonArray($inputString);

        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    public function testPrepareForShow()
    {
        $projectDir = '/tmp';
        $hostUrl = 'http://localhost';
        $recipeService = new RecipeService($projectDir, $hostUrl);
        $user = new User();
        $user->setEmail('test@example.com');

        $recipe = new Recipe();
        $recipe->setId(1);
        $recipe->setTitle('Test Recipe');
        $recipe->setNer(json_encode(['ingredient1', 'ingredient2']));
        $recipe->setDirections(json_encode(['step1', 'step2']));
        $recipe->setIngredients(json_encode(['ingredient1', 'ingredient2']));
        $recipe->setLink('http://example.com');
        $recipe->setSource('Book');
        $recipe->setSite('example.com');
        $recipe->setUser($user);
        $recipe->setImageUrl('http://example.com/image.jpg');

        $uiRecipe = $recipeService->prepareForShow($recipe);

        $this->assertInstanceOf(UiRecipe::class, $uiRecipe);
        $this->assertEquals(1, $uiRecipe->getId());
        $this->assertEquals('Test Recipe', $uiRecipe->getTitle());
        $this->assertEquals(['ingredient1', 'ingredient2'], $uiRecipe->getIngredients());
        $this->assertEquals(['step1', 'step2'], $uiRecipe->getDirections());
        $this->assertEquals('http://example.com', $uiRecipe->getLink());
        $this->assertEquals('Book', $uiRecipe->getSource());
        $this->assertEquals('ingredient1, ingredient2', $uiRecipe->getNer());
        $this->assertEquals('example.com', $uiRecipe->getSite());
        $this->assertEquals($user, $uiRecipe->getUser());
        $this->assertEquals('http://example.com/image.jpg', $uiRecipe->getImageUrl());
    }

    public function testPrepareForShowWithInvalidImageUrl()
    {
        $projectDir = '/tmp';
        $hostUrl = 'http://localhost';
        $recipeService = new RecipeService($projectDir, $hostUrl);
        $user = new User();
        $user->setEmail('test@example.com');

        $recipe = new Recipe();
        $recipe->setId(1);
        $recipe->setTitle('Test Recipe');
        $recipe->setNer(json_encode(['ingredient1', 'ingredient2']));
        $recipe->setDirections(json_encode(['step1', 'step2']));
        $recipe->setIngredients(json_encode(['ingredient1', 'ingredient2']));
        $recipe->setLink('http://example.com');
        $recipe->setSource('Book');
        $recipe->setSite('example.com');
        $recipe->setUser($user);
        $recipe->setImageUrl('invalid-url');

        $uiRecipe = $recipeService->prepareForShow($recipe);

        $this->assertInstanceOf(UiRecipe::class, $uiRecipe);
        $this->assertEquals(1, $uiRecipe->getId());
        $this->assertEquals('Test Recipe', $uiRecipe->getTitle());
        $this->assertEquals(['ingredient1', 'ingredient2'], $uiRecipe->getIngredients());
        $this->assertEquals(['step1', 'step2'], $uiRecipe->getDirections());
        $this->assertEquals('http://example.com', $uiRecipe->getLink());
        $this->assertEquals('Book', $uiRecipe->getSource());
        $this->assertEquals('ingredient1, ingredient2', $uiRecipe->getNer());
        $this->assertEquals('example.com', $uiRecipe->getSite());
        $this->assertEquals($user, $uiRecipe->getUser());
        $this->assertNull($uiRecipe->getImageUrl());
    }
}