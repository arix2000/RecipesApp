<?php

namespace App\Tests\Service;

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
}