<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    #[Route('/', name: 'recipes')]
    public function recipes(): Response
    {
        $recipes = $this->recipeRepository->findAll();
        $recipes = array_slice($recipes, 0, 20);
        array_map(function ($recipe) {
            $readableString = implode(", ", json_decode($recipe->getNer()));
            $recipe->setNer($readableString);
        }, $recipes);
        return $this->render('recipe/index.html.twig', ["recipes" => $recipes]);
    }

    #[Route('/recipe/{id}', name: 'recipe', methods: ["GET"])]
    public function recipe($id): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $ner = implode(", ", json_decode($recipe->getNer()));
        $directions = json_decode($recipe->getDirections());
        $ingredients = json_decode($recipe->getIngredients());

        return $this->render('recipe/recipe_details.html.twig',
            [
                "recipe" => $recipe,
                "ner" => $ner,
                "directions" => $directions,
                "ingredients" => $ingredients,
            ]);
    }

    #[Route('/your-recipes', name: 'your_recipes')]
    public function yourRecipes(): Response
    {
        return $this->render('');
    }

    #[Route('/search', name: 'search')]
    public function search(): Response
    {
        return $this->render('');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('');
    }
}
