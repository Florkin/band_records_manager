<?php

namespace App\Repository;

use App\Entity\Record;
use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    public function findBySongQuery(Song $song)
    {
        return $this->createQueryBuilder('r')
            ->where('r.song = :song')
            ->setParameter('song', $song)
            ->orderBy('r.recordedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
