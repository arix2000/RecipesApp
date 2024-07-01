<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Services\RecipeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

class ApiRecipeController extends AbstractController
{
    private RecipeService $recipeService;

    public function __construct(
        RecipeRepository $recipeRepository,
        RequestStack     $requestStack,
        string           $projectDir)
    {
        $request = $requestStack->getCurrentRequest();
        $hostUrl = $request->getSchemeAndHttpHost();
        $this->recipeService = new RecipeService($recipeRepository, $projectDir, $hostUrl);
    }

    #[Route('/api/recipes', name: 'api_recipes', methods: ['GET'])]
    public function apiRecipes(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $recipePagination = $this->recipeService->getRecipePagination($request, $paginator);
        $recipes = $recipePagination->getRecipes();
        $pagination = $recipePagination->getPagination();

        return $this->json([
            'items' => $recipes,
            'pagination' => [
                'current_page' => $pagination->getCurrentPageNumber(),
                'total_items' => $pagination->getTotalItemCount(),
                'items_per_page' => $pagination->getItemNumberPerPage(),
                'total_pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            ],
        ]);
    }
}
