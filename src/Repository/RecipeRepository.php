<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Recipe::class);
        $this->entityManager = $entityManager;
    }

    public function findAllByTermQuery(string $query): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->andWhere('LOWER(r.title) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%');
    }

    public function findAllByUserIdQuery(int $userId): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->setParameter('userId', $userId);
    }

    public function add(Recipe $recipe): void
    {
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();
    }
}
