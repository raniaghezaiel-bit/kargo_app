<?php

namespace App\Repository;

use App\Entity\Horaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HoraireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horaire::class);
    }

    /**
     * Trouve les horaires actifs
     */
    public function findActifs(): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.statut = :statut')
            ->setParameter('statut', 'actif')
            ->orderBy('h.villeDepart', 'ASC')
            ->addOrderBy('h.heureDepart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche par ville de départ et/ou arrivée
     */
    public function findByVilles(?string $villeDepart, ?string $villeArrivee): array
    {
        $qb = $this->createQueryBuilder('h');

        if ($villeDepart) {
            $qb->andWhere('h.villeDepart = :depart')
               ->setParameter('depart', $villeDepart);
        }

        if ($villeArrivee) {
            $qb->andWhere('h.villeArrivee = :arrivee')
               ->setParameter('arrivee', $villeArrivee);
        }

        return $qb->orderBy('h.heureDepart', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}