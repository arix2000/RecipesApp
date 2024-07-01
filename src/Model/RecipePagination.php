<?php

namespace App\Model;

use Knp\Component\Pager\Pagination\PaginationInterface;

class RecipePagination
{
    private PaginationInterface $pagination;
    private array $recipes;

    public function __construct(array $recipes, PaginationInterface $pagination)
    {
        $this->recipes = $recipes;
        $this->pagination = $pagination;
    }

    public function getPagination(): PaginationInterface
    {
        return $this->pagination;
    }

    public function getRecipes(): array
    {
        return $this->recipes;
    }
}