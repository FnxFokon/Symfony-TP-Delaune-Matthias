<?php

namespace App\Repository;

use App\Entity\Bien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bien>
 *
 * @method Bien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bien[]    findAll()
 * @method Bien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bien::class);
    }

    public function getBiens()
    {
        //appel de l'entity manager
        $entityManager = $this->getEntityManager();
        //on crée la query
        $query = $entityManager->createQuery(
            'SELECT b.id, b.imagePath, tb.label, tb.price, tb.maxPeople
                    FROM App\Entity\Bien b
                    JOIN b.typeBien tb'
        );

        return $query->getResult();
    }

    public function findBiensWithUser()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT b.id, tb.label, tb.maxPeople, u.email, u.roles, u.lastname, u.firstname, u.phone
            FROM App\Entity\Bien b
            JOIN b.typeBien tb
            JOIN b.user u'
        );

        return $query->getResult();
    }

    // Méthode pour trouver un bien en fonction de son ID
    public function findBienById($id)
    {
        //appel de l'entity manager
        $entityManager = $this->getEntityManager();
        //on crée la query
        $query = $entityManager->createQuery(
            'SELECT b.id, b.imagePath, b.size, b.description, tb.label, tb.price, tb.maxPeople
            FROM App\Entity\Bien b
            JOIN b.typeBien tb
            WHERE b.id =:id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    public function findBienByTypeBien($id)
    {
        //appel de l'entity manager
        $entityManager = $this->getEntityManager();
        //on crée la query
        $query = $entityManager->createQuery(
            'SELECT b.id, b.imagePath, tb.label, tb.price, tb.maxPeople
                    FROM App\Entity\Bien b
                    JOIN b.typeBien tb
                    WHERE tb.id =:id'
        )->setParameter('id', $id);

        return $query->getResult();
    }
}
