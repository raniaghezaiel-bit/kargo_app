<?php

namespace App\Repository;

use App\Entity\Bus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bus>
 */
class BusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bus::class);
    }

    /**
     * Récupérer tous les bus disponibles (non assignés à un trajet actif)
     */
    public function findAvailableBuses(): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.trajets', 't')
            ->where('t.id IS NULL')
            ->orWhere('t.date < :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('b.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Rechercher des bus par numéro ou type
     */
    public function searchBus(string $query): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.numero LIKE :query')
            ->orWhere('b.type LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('b.numero', 'ASC')
            ->getQuery()
            ->getResult();
    }
}