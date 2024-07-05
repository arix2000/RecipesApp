<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Services\ApiFormatter;
use App\Services\PagingService;
use App\Services\RecipeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;

class ApiRecipeController extends AbstractController
{
    private PagingService $pagingService;
    private ApiFormatter $apiFormatter;

    public function __construct(
        PagingService $pagingService,
        ApiFormatter  $apiFormatter
    )
    {
        $this->pagingService = $pagingService;
        $this->apiFormatter = $apiFormatter;
    }

    #[Route('/api/recipes', name: 'api_recipes', methods: ['GET'])]
    public function apiRecipes(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $recipePagination = $this->pagingService->getRecipePagination($request, $paginator);
        $recipes = $recipePagination->getRecipes();
        $pagination = $recipePagination->getPagination();

        $data = [
            'items' => $recipes,
            'pagination' => [
                'current_page' => $pagination->getCurrentPageNumber(),
                'total_items' => $pagination->getTotalItemCount(),
                'items_per_page' => $pagination->getItemNumberPerPage(),
                'total_pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            ],
        ];

        return $this->apiFormatter->formatResponse($data);
    }

    #[Route('/api/search', name: 'api_search', methods: ['GET'])]
    public function apiSearchRecipes(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        if ($request->query->get('search') === null) {
            return $this->apiFormatter->formatError("No search parameter provided.");
        }
        $recipePagination = $this->pagingService->getRecipePagination($request, $paginator, true);
        $recipes = $recipePagination->getRecipes();
        $pagination = $recipePagination->getPagination();
        return $this->apiFormatter->formatResponse($this->pagingService->getPaginatedResponse($pagination, $recipes));
    }
}
