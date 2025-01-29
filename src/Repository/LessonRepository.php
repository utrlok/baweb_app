<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class LessonRepository extends EntityRepository
{
    public function getMaxPosition(): int
    {
        $query = $this->createQueryBuilder('l');

        $query->select('MAX(l.position)');

        return (int)$query->getQuery()->getSingleScalarResult();
    }
}