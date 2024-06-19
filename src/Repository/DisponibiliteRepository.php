<?php

namespace App\Repository;

use App\Entity\Disponibilite;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Disponibilite>
 */
class DisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disponibilite::class);
    }

    public function touteLesDisponibilites(): array
        {
            $dateDuJour = new DateTime();
            $query = $this->findVisibleQuery();
            $query = $query
                ->andWhere('b.dateFin >= :dateJour')
                ->setParameter('dateJour', $dateDuJour);

            $query = $query
                ->andWhere('b.statut = :statut')
                ->setParameter('statut', "O");    
            
            return $query->getQuery()->getResult();
        }

        private function findVisibleQuery(): QueryBuilder
        {
            return $this->createQueryBuilder('b');
        }
    //    /**
    //     * @return Disponibilite[] Returns an array of Disponibilite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Disponibilite
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
