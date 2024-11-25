<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/** @extends EntityRepository<Section> */
class SectionRepository extends EntityRepository
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ClassMetadata $class,
    ) {
        parent::__construct($entityManager, $class);
    }

    public function findOneById(int $id): ?Section
    {
        /** @var ?Section $section */
        $section = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $section;
    }
}