<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findByTermQuery(string $query): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->andWhere('LOWER(r.title) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%');
    }

    public function findByUserQuery(int $userId): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->setParameter('userId', $userId);
    }
}
