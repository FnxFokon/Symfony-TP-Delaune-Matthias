<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function getReservationByBienId($id)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT r.dateBegin, r.dateFin, r.price, u.email, u.lastname, u.firstname, u.phone
            FROM App\Entity\Reservation r
            JOIN r.bien b
            JOIN b.typeBien tb
            JOIN r.user u
            WHERE r.bien = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }

    public function getReservationByUserId($id)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tb.label, tb.price as priceFlat, tb.maxPeople, r.dateBegin, r.dateFin, r.stayDay, r.people, r.children, r.baby, r.accesspool, r.price, u.email, u.lastname, u.firstname, u.phone
            FROM App\Entity\Reservation r
            JOIN r.bien b
            JOIN b.typeBien tb
            JOIN r.user u
            WHERE r.user = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }
}
