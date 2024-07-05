<?php

namespace App\Controller;

use App\Constants\RoutesConst;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Services\ApiFormatter;
use App\Services\PagingService;
use App\Services\RecipeService;
use Knp\Component\Pager\PaginatorInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiRecipeController extends AbstractController
{
    private PagingService $pagingService;
    private RecipeService $recipeService;
    private ApiFormatter $apiFormatter;
    private RecipeRepository $recipeRepository;
    private UserRepository $userRepository;

    public function __construct(
        PagingService    $pagingService,
        ApiFormatter     $apiFormatter,
        RecipeRepository $recipeRepository,
        UserRepository   $userRepository,
        RecipeService $recipeService
    )
    {
        $this->pagingService = $pagingService;
        $this->apiFormatter = $apiFormatter;
        $this->recipeRepository = $recipeRepository;
        $this->userRepository = $userRepository;
        $this->recipeService = $recipeService;
    }

    #[Route('/api/recipes', name: 'api_recipes', methods: ['GET'])]
    public function apiRecipes(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $recipePagination = $this->pagingService->getRecipePagination($request, $paginator);
        $recipes = $recipePagination->getRecipes();
        $pagination = $recipePagination->getPagination();

        return $this->apiFormatter->formatResponse(
            $this->pagingService->getPaginatedResponse($pagination, $recipes));
    }

    #[Route('/api/search', name: 'api_search', methods: ['GET'])]
    public function apiSearchRecipes(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        if ($request->query->get(RoutesConst::SEARCH_PARAM) === null) {
            return $this->apiFormatter->formatError("No search parameter provided.");
        }
        $recipePagination = $this->pagingService->getRecipePagination($request, $paginator, true);
        $recipes = $recipePagination->getRecipes();
        $pagination = $recipePagination->getPagination();

        return $this->apiFormatter->formatResponse(
            $this->pagingService->getPaginatedResponse($pagination, $recipes));
    }

    #[Route('/api/recipe/{id}', name: 'api_recipe', methods: ['GET'])]
    public function apiRecipeDetails($id): JsonResponse
    {
        $data = $this->recipeRepository->findOneBy(['id' => $id]);
        if (!$data) {
            return $this->apiFormatter->formatError("Recipe not found.", Response::HTTP_NOT_FOUND);
        }
        return $this->apiFormatter->formatResponse($data->toMap());
    }

    #[Route('/api/recipe/add', name: 'api_recipe_add', methods: ['POST'])]
    public function apiAddRecipe(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->apiFormatter->formatError("Invalid JSON data.");
        }

        $userId = (string)$data['userId'];
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        try {
            $validateResult = $this->recipeService->validateAndConvertRecipe($data, $user);
            if ($validateResult instanceof Recipe) {
                $this->recipeRepository->add($validateResult);
                return $this->apiFormatter->formatResponse(['result' => 'OK']);
            } else {
                return $this->apiFormatter->formatError($validateResult);
            }
        } catch (\Exception $e) {
            return $this->apiFormatter->formatError($e->getMessage());
        }
    }
}
