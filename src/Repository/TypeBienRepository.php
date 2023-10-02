<?php

namespace App\Repository;

use App\Entity\TypeBien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeBien>
 *
 * @method TypeBien|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeBien|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeBien[]    findAll()
 * @method TypeBien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeBienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeBien::class);
    }

    public function getCountTypebienByBien()
    {
        //appel de l'entity manager
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tb.id, tb.label, tb.maxPeople, tb.price, COUNT(b.id) AS total
            FROM App\Entity\Bien b
            JOIN b.typeBien tb
            GROUP BY tb.id'
        );

        return $query->getResult();
    }
}
