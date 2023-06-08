<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

/**
 * @extends ServiceEntityRepository<Livre>
 *
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function save(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Livre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNombreEmpruntsParLivre()
    {
        $entityManager = $this->getEntityManager();
    
        $query = $entityManager->createQuery('
            SELECT l.titre AS titre, COUNT(e) AS nombreEmprunts
            FROM App\Entity\Livre l
            JOIN l.emprunts e
            GROUP BY l.titre
        ');
    
        return $query->getResult();
    }

    
    public function findLivresEmpruntes()
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery('
        SELECT livre
        FROM App\Entity\Livre livre
        JOIN livre.emprunts emprunt
        WHERE emprunt.date_retour > :dateActuelle
    ')->setParameter('dateActuelle', new \DateTime());

    return $query->getResult();
}


    /**
     * @param Auteur $auteur
     * @return Livre[]
     */
    public function findByAuteur(Auteur $auteur): array
    {
    return $this->createQueryBuilder('l')
        ->andWhere('l.auteur = :auteur')
        ->setParameter('auteur', $auteur)
        ->getQuery()
        ->getResult();
}

//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
