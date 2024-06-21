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
            $dateDuJour = new DateTime(); # on récupère la date du jour
            $query = $this->findVisibleQuery();
            $query = $query
                ->andWhere('b.dateFin >= :dateJour')
                ->setParameter('dateJour', $dateDuJour); # on réalise une recherche des disponibilités où la date de fin est supérieur à la date du jour

            $query = $query
                ->andWhere('b.statut = :statut')
                ->setParameter('statut', "O");    # on ajoute une deuxième condition qui est de récupérer les disponibilités qui ont le statut O donc disponible
            
            return $query->getQuery()->getResult();
        }

        public function searchDispo(DisponibiliteSearch $search): array
        {
            $query = $this->findVisibleQuery();
            

            if ($search->getDateDebut() && $search->getDateFin()) { # si la date de fin et de début sont données par l'utilisateur dans le filtre alors
                $query = $query
                    ->andWhere('(b.dateDebut BETWEEN :dateDebut AND :dateFin) OR (b.dateFin BETWEEN :dateDebut AND :dateFin)') # on ajoute une condition pour que la date de début soit entre les dates de début et fin des lignes présentes dans la base de données et on fait la même chose pour la date de fin
                    ->setParameter('dateDebut', $search->getDateDebut())
                    ->setParameter('dateFin', $search->getDateFin());
            } elseif ($search->getDateDebut()) { # sinon si seulement la date de début a été donnée : 
                $query = $query
                    ->andWhere('(b.dateDebut >= :dateDebut) OR (b.dateFin >= :dateDebut)') # on ajoute une condition qui permet de récupérer les lignes où la date de début  est soit supérieur ou égale aux dates de début dans la base de données, soit supérieur ou égal à la date de fin
                    ->setParameter('dateDebut', $search->getDateDebut());
            } elseif ($search->getDateFin()) { # sinon si seulement la date de fin a été donnée : 
                $query = $query
                    ->andWhere('(b.dateDebut <= :dateFin) OR (b.dateFin <= :dateFin)') # on ajoute une condition qui permet de récupérer les lignes où la date de fin donnée par l'utilisateur est soit inférieur ou égale aux dates de début dans la base de données, soit inférieur ou égal à la date de fin
                    ->setParameter('dateFin', $search->getDateFin());
            }

            if ($search->getPrixMax()) { # si un prix max a été donné :
                $query = $query
                    ->andWhere('b.prixParJour <= :prixMax')
                    ->setParameter('prixMax', $search->getPrixMax()); # on ajoute une condition qui récupère toutes les disponibilités avec un prix inférieur ou égal à celui que l'utilisateur a donné
            }
            $query = $query
                ->andWhere('b.statut = :statut')
                ->setParameter('statut', "O");    # on ajoute une deuxième condition qui est de récupérer les disponibilités qui ont le statut O donc disponible

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
