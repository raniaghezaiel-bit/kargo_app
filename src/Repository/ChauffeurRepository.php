<?php

namespace App\Repository;

use App\Entity\Chauffeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chauffeur>
 */
class ChauffeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chauffeur::class);
    }

    /**
     * Recherche des chauffeurs par nom, prénom ou CIN
     */
    public function search(string $query): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.nom LIKE :query')
            ->orWhere('c.prenom LIKE :query')
            ->orWhere('c.cin LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les chauffeurs actifs
     */
    public function findActifs(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'actif')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les chauffeurs avec permis expiré
     */
    public function findPermisExpires(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.dateExpirationPermis < :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('c.dateExpirationPermis', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les chauffeurs par statut
     */
    public function countByStatut(): array
    {
        return [
            'total' => $this->count([]),
            'actifs' => $this->count(['statut' => 'actif']),
            'inactifs' => $this->count(['statut' => 'inactif']),
            'en_conge' => $this->count(['statut' => 'en_conge']),
            'suspendus' => $this->count(['statut' => 'suspendu']),
        ];
    }
}