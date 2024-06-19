<?php

namespace App\Repository;

use App\Entity\Disponibilite;
use App\Entity\DisponibiliteSearch;
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

        public function searchDispo(DisponibiliteSearch $search): array
        {
            $query = $this->findVisibleQuery();

            if ($search->getDateDebut() && $search->getDateFin()) {
                $query = $query
                    ->andWhere('(b.dateDebut BETWEEN :dateDebut AND :dateFin) OR (b.dateFin BETWEEN :dateDebut AND :dateFin)')
                    ->setParameter('dateDebut', $search->getDateDebut())
                    ->setParameter('dateFin', $search->getDateFin());
            } elseif ($search->getDateDebut()) {
                $query = $query
                    ->andWhere('(b.dateDebut >= :dateDebut) OR (b.dateFin >= :dateDebut)')
                    ->setParameter('dateDebut', $search->getDateDebut());
            } elseif ($search->getDateFin()) {
                $query = $query
                    ->andWhere('(b.dateDebut <= :dateFin) OR (b.dateFin <= :dateFin)')
                    ->setParameter('dateFin', $search->getDateFin());
            }

            if ($search->getPrixMax()) {
                $query = $query
                    ->andWhere('b.prixParJour <= :prixMax')
                    ->setParameter('prixMax', $search->getPrixMax());
            }

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
