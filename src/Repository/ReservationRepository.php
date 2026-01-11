<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Passager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByPassager(Passager $passager)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.passager = :passager')
            ->setParameter('passager', $passager)
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveReservations(Passager $passager)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.passager = :passager')
            ->andWhere('r.statut IN (:statuts)')
            ->setParameter('passager', $passager)
            ->setParameter('statuts', ['en_attente', 'confirmee'])
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}