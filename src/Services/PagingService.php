<?php

namespace App\Services;

use App\Model\RecipePagination;
use App\Repository\RecipeRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;

class PagingService
{
    private RecipeRepository $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    function getRecipePagination($request, PaginatorInterface $paginator, $bySearchQuery = false): RecipePagination
    {
        $query = $this->recipeRepository->createQueryBuilder('r');

        $searchTerm = $request->query->get('search', '');
        if ($bySearchQuery) {
            if (!empty($searchTerm)) {
                $query = $this->recipeRepository->findByTermQuery($searchTerm);
            }
        }

        return $this->getPaginatedData($query, $paginator, $request, $searchTerm);
    }

    function getUserRecipesPagination(int $userId, $request, PaginatorInterface $paginator): RecipePagination
    {
        return $this->getPaginatedData(
            $this->recipeRepository->findByUserQuery($userId),
            $paginator,
            $request);
    }

    private function getPaginatedData(
        QueryBuilder $queryBuilder, $paginator, $request, $searchTerm = ""): RecipePagination
    {
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            24
        );

        $recipes = [];
        foreach ($pagination as $recipe) {
            $readableString = implode(", ", json_decode($recipe->getNer()));
            $recipe->setNer($readableString);
            $imageUrl = $recipe->getImageUrl();
            $isValidUrl = filter_var($imageUrl, FILTER_VALIDATE_URL) !== false;
            $recipe->setImageUrl($isValidUrl ? $imageUrl : null);
            $recipes[] = $recipe->toMap($recipe);
        }
        return new RecipePagination($recipes, $pagination, $searchTerm);
    }
}