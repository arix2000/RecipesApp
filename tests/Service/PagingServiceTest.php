<?php

namespace App\Tests\Service;

use App\Model\RecipePagination;
use App\Repository\RecipeRepository;
use App\Services\PagingService;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PagingServiceTest extends TestCase
{
    public function testGetRecipePagination()
    {
        $recipeRepository = $this->createMock(RecipeRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $recipeRepository->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $paginator = $this->createMock(PaginatorInterface::class);
        $request = Request::create('/?search=test');
        $pagingService = new PagingService($recipeRepository);
        $result = $pagingService->getRecipePagination($request, $paginator, true);

        $this->assertInstanceOf(RecipePagination::class, $result);
    }

    public function testGetUserRecipesPagination()
    {
        $recipeRepository = $this->createMock(RecipeRepository::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $recipeRepository->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $paginator = $this->createMock(PaginatorInterface::class);
        $request = Request::create('/');
        $pagingService = new PagingService($recipeRepository);
        $result = $pagingService->getUserRecipesPagination(1, $request, $paginator);

        $this->assertInstanceOf(RecipePagination::class, $result);
    }
}
