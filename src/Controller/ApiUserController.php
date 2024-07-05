<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\ApiFormatter;
use App\Services\PagingService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiUserController extends AbstractController
{
    private PagingService $pagingService;
    private ApiFormatter $apiFormatter;
    private UserRepository $repository;

    public function __construct(
        PagingService $pagingService,
        ApiFormatter  $apiFormatter,
        UserRepository $repository
    )
    {
        $this->pagingService = $pagingService;
        $this->apiFormatter = $apiFormatter;
        $this->repository = $repository;
    }

    #[Route('/api/user/recipes/{id}', name: 'api_user_recipes', methods: ['GET'])]
    public function apiUserRecipes($id, Request $request, PaginatorInterface $paginator): Response
    {
        $isUserNotExist = $this->repository->findOneBy(['id' => $id]) === null;
        if ($isUserNotExist)
            return $this->apiFormatter->formatError("User not exists!");
        $recipesPagination = $this->pagingService->getUserRecipesPagination($id, $request, $paginator);
        $recipes = $recipesPagination->getRecipes();
        $pagination = $recipesPagination->getPagination();
        return $this->apiFormatter->formatResponse($this->pagingService->getPaginatedResponse($pagination, $recipes));
    }
}
