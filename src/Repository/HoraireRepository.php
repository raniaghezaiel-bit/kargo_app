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
            ->orderBy('h.dateDepart', 'ASC')
            ->addOrderBy('h.heureDepart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche par ville de départ et/ou arrivée
     */
    public function findByVilles(?string $villeDepart, ?string $villeArrivee): array
    {
        $qb = $this->createQueryBuilder('h')
            ->join('h.trajet', 't');

        if ($villeDepart) {
            $qb->andWhere('t.villeDepart = :depart')
               ->setParameter('depart', $villeDepart);
        }

        if ($villeArrivee) {
            $qb->andWhere('t.villeArrivee = :arrivee')
               ->setParameter('arrivee', $villeArrivee);
        }

        return $qb->orderBy('h.heureDepart', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Recherche des horaires disponibles pour la réservation
     */
    public function findAvailableHoraires(string $villeDepart, string $villeArrivee, \DateTime $date): array
    {
        return $this->createQueryBuilder('h')
            ->join('h.trajet', 't')
            ->join('h.bus', 'b')
            ->where('t.villeDepart = :depart')
            ->andWhere('t.villeArrivee = :arrivee')
            ->andWhere('h.dateDepart = :date')
            ->andWhere('h.statut = :statut')
            ->andWhere('h.placesDisponibles > 0')
            ->setParameter('depart', $villeDepart)
            ->setParameter('arrivee', $villeArrivee)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('statut', 'actif')
            ->orderBy('h.heureDepart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}