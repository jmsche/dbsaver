<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Backup;
use App\Entity\Database;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Backup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backup[]    findAll()
 * @method Backup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backup::class);
    }

    public function remove(Backup $backup)
    {
        $this->getEntityManager()->remove($backup);
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return Backup[]
     */
    public function getActiveBackups(Database $database): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('b')
            ->from(Backup::class, 'b')
            ->innerJoin('b.database', 'd')
            ->where(
                $qb->expr()->eq(
                    'd.id',
                    $database->getId()
                )
            )
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($database->getMaxBackups() - 1);

        return $qb->getQuery()->getResult();
    }
}
