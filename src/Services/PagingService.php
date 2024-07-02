<?php

namespace App\Services;

use App\Model\RecipePagination;
use App\Repository\RecipeRepository;
use Doctrine\ORM\QueryBuilder;

class PagingService
{
    private RecipeRepository $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    function getRecipePagination($request, $paginator, $bySearchQuery = false): RecipePagination
    {
        $queryBuilder = $this->getQueryBuilder();

        $searchTerm = $request->query->get('search', '');
        if ($bySearchQuery) {
            if (!empty($searchTerm)) {
                $queryBuilder->andWhere('LOWER(r.title) LIKE LOWER(:searchTerm)')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');
            }
        }

        return $this->getPaginatedData($queryBuilder, $paginator, $request, $searchTerm);
    }

    function getUserRecipesPagination(int $userId, $request, $paginator): RecipePagination
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->andWhere('r.user = :userId')
            ->setParameter('userId', $userId);

        return $this->getPaginatedData($queryBuilder, $paginator, $request);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->recipeRepository->createQueryBuilder('r');
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