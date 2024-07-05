<?php

namespace App\Tests\Service;

use App\Entity\Recipe;
use App\Entity\User;
use App\Model\SourceChoices;
use App\Services\RecipeService;
use PHPUnit\Framework\TestCase;

class RecipeServiceTest extends TestCase
{
    private RecipeService $recipeService;

    protected function setUp(): void
    {
        $this->recipeService = new RecipeService();
    }

    public function testStringToJsonArray()
    {
        $inputString = "line1\nline2\nline3";
        $expectedOutput = json_encode(['line1', 'line2', 'line3']);

        $output = $this->recipeService->stringToJsonArray($inputString);

        $this->assertJsonStringEqualsJsonString($expectedOutput, $output);
    }

    public function testValidateAndConvertRecipeWithValidData()
    {
        $user = new User();
        $data = [
            'title' => 'Test Recipe',
            'ingredients' => 'Ingredient 1, Ingredient 2',
            'directions' => 'Step 1, Step 2',
            'link' => 'https://example.com',
            'source' => SourceChoices::WEB->name,
            'ner' => 'NER data',
            'site' => 'https://example.com',
            'imageUrl' => 'https://example.com/image.jpg'
        ];

        $recipe = $this->recipeService->validateAndConvertRecipe($data, $user);

        $this->assertInstanceOf(Recipe::class, $recipe);
        $this->assertEquals($data['title'], $recipe->getTitle());
        $this->assertEquals($data['link'], $recipe->getLink());
        $this->assertEquals($data['source'], $recipe->getSource());
    }

    public function testValidateAndConvertRecipeWithInvalidSource()
    {
        $user = new User();
        $data = [
            'title' => 'Test Recipe',
            'ingredients' => 'Ingredient 1, Ingredient 2',
            'directions' => 'Step 1, Step 2',
            'link' => 'https://example.com',
            'source' => 'INVALID_SOURCE',
            'ner' => 'NER data',
            'site' => 'https://example.com',
            'imageUrl' => 'https://example.com/image.jpg'
        ];

        $result = $this->recipeService->validateAndConvertRecipe($data, $user);

        $this->assertIsString($result);
        $this->assertStringContainsString('Source have unacceptable value', $result);
    }

    public function testValidateAndConvertRecipeWithMissingUser()
    {
        $data = [
            'title' => 'Test Recipe',
            'ingredients' => 'Ingredient 1, Ingredient 2',
            'directions' => 'Step 1, Step 2',
            'link' => 'https://example.com',
            'source' => SourceChoices::WEB->name,
            'ner' => 'NER data',
            'site' => 'https://example.com',
            'imageUrl' => 'https://example.com/image.jpg'
        ];

        $result = $this->recipeService->validateAndConvertRecipe($data, null);

        $this->assertIsString($result);
        $this->assertEquals('User not found', $result);
    }

    public function testValidateAndConvertRecipeWithInvalidUrl()
    {
        $user = new User();
        $data = [
            'title' => 'Test Recipe',
            'ingredients' => 'Ingredient 1, Ingredient 2',
            'directions' => 'Step 1, Step 2',
            'link' => 'invalid-url',
            'source' => SourceChoices::WEB->name,
            'ner' => 'NER data',
            'site' => 'invalid-url',
            'imageUrl' => 'https://example.com/image.jpg'
        ];

        $result = $this->recipeService->validateAndConvertRecipe($data, $user);

        $this->assertIsString($result);
        $this->assertEquals("Link or site is not a valid URL, it is required when source = WEB", $result);
    }
}