<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    /**
     * Recherche les annonces en fonction des mot-clés saisis dans le formulaire
     * @param mixed $mots 
     * @return mixed 
     */
    public function search($mots = null, $categorie = null)
    {
        $query = $this->createQueryBuilder('a');
        $query->where('a.active = 1');

        if ($mots != null) {
            $query->andWhere('MATCH_AGAINST(a.title, a.content) AGAINST(:mots boolean)>0')
                ->setParameter('mots', $mots);
        }

        if ($categorie != null) {
            $query->leftJoin('a.categories', 'c')
                ->andWhere('c.id = :id')
                ->setParameter('id', $categorie);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Retourne le nombre d'annonces par date
     * @return mixed 
     */
    public function countByDate()
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT 
            COUNT(a) as count,
            SUBSTRING(a.created_at, 1, 10) as dateAnnonces
            FROM App\Entity\Annonces a
            GROUP BY dateAnnonces'
        );

        return $query->getResult();
    }

    // /**
    //  * @return Annonces[] Returns an array of Annonces objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
