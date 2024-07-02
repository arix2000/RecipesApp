<?php

namespace App\Model;

use Knp\Component\Pager\Pagination\PaginationInterface;

class RecipePagination
{
    private PaginationInterface $pagination;
    private array $recipes;
    private string $searchTerm;

    public function __construct(array $recipes, PaginationInterface $pagination, string $searchTerm)
    {
        $this->recipes = $recipes;
        $this->pagination = $pagination;
        $this->searchTerm = $searchTerm;
    }

    public function getPagination(): PaginationInterface
    {
        return $this->pagination;
    }

    public function getRecipes(): array
    {
        return $this->recipes;
    }

    public function getSearchTerm(): string {
        return $this->searchTerm;
    }
}