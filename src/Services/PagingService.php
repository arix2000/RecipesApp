<?php

namespace App\Services;

use App\Constants\RoutesConst;
use App\Model\RecipePagination;
use App\Repository\RecipeRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PagingService
{
    private RecipeRepository $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    function getRecipePagination(
        $request,
        PaginatorInterface $paginator,
        $bySearchQuery = false,
        $shouldFormatNer = false,
    ): RecipePagination
    {
        $query = $this->recipeRepository->createQueryBuilder('r');

        $searchTerm = $request->query->get(RoutesConst::SEARCH_PARAM, '');
        if ($bySearchQuery) {
            if (!empty($searchTerm)) {
                $query = $this->recipeRepository->findAllByTermQuery($searchTerm);
            }
        }

        return $this->getPaginatedData(
            $query, $paginator, $request, $searchTerm, $shouldFormatNer);
    }

    function getUserRecipesPagination(
        int                $userId,
        Request            $request,
        PaginatorInterface $paginator,
        bool               $shouldFormatNer = false,
    ): RecipePagination
    {
        return $this->getPaginatedData(
            $this->recipeRepository->findAllByUserIdQuery($userId),
            $paginator,
            $request,
            shouldFormatNer: $shouldFormatNer);
    }

    private function getPaginatedData(
        QueryBuilder       $queryBuilder,
        PaginatorInterface $paginator,
        Request            $request,
        string             $searchTerm = "",
        bool               $shouldFormatNer = false): RecipePagination
    {
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            24
        );

        $recipes = [];
        foreach ($pagination as $recipe) {
            if ($shouldFormatNer) {
                $readableString = implode(", ", json_decode($recipe->getNer()));
                $recipe->setNer($readableString);
            }
            $imageUrl = $recipe->getImageUrl();
            $isValidUrl = filter_var($imageUrl, FILTER_VALIDATE_URL) !== false;
            $recipe->setImageUrl($isValidUrl ? $imageUrl : null);
            $recipes[] = $recipe->toMap($recipe);
        }
        return new RecipePagination($recipes, $pagination, $searchTerm);
    }

    public function getPaginatedResponse(PaginationInterface $pagination, array $items): array
    {
        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $pagination->getCurrentPageNumber(),
                'total_items' => $pagination->getTotalItemCount(),
                'items_per_page' => $pagination->getItemNumberPerPage(),
                'total_pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            ],
        ];
    }
}