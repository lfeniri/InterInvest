<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Company::class);
    }

    // /**
    //  * @return Company[] Returns an array of Company objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Company
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */




public function findAllPagineTrie($page,$maxSize){

    if (!is_numeric($page)) {
        throw new InvalidArgumentException(
            'try with correct number of page please'
        );
    }

    if ($page < 1) {
        throw new NotFoundHttpException('page need to be greater than 0 !');
    }

  
    $qb = $this->createQueryBuilder('c')
                ->orderBy('c.name', 'DESC');
    $query = $qb->getQuery();
    
    $query->setFirstResult($maxSize*($page-1))->setMaxResults($maxSize);
    $paginator = new Paginator($query);
    
    if ( ($paginator->count() <= $maxSize*($page-1)) && $page != 1) {
        throw new NotFoundHttpException('Page not exist.');
    }

    return [ 
        'list'      => $paginator, 
        'nbPages'   => ceil($paginator->count()/$maxSize),
    ];
}

}
