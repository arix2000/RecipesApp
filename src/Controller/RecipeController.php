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

    #[Route('/recipe', name: 'app_recipe')]
    public function index(): Response
    {
        return $this->render('recipe/index.html.twig');
    }

    #[Route('/', name: 'recipe')]
    public function home(): Response
    {
        $recipes = $this->recipeRepository->findAll();
        $recipes = array_slice($recipes, 0, 20);
        return $this->render('recipe/index.html.twig', ["recipes" => $recipes]);
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
