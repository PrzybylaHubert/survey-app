<?php

namespace App\Repository;

use App\Entity\Survey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Survey>
 */
class SurveyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Survey::class);
    }

    public function findFullSurveyById(int $surveyId, bool $getAnswers = false): ?Survey
    {
        $builder = $this->createQueryBuilder('s')
            ->leftJoin('s.surveySections', 'sec')
            ->leftJoin('sec.questions', 'q')
            ->leftJoin('q.offeredAnswers', 'oa')
            ->addSelect('sec', 'q', 'oa')
            ->where('s.id = :surveyId')
            ->setParameter('surveyId', $surveyId);
        
        if ($getAnswers === true) {
            $builder
                ->leftJoin('s.surveyAssignments', 'sa')
                ->leftJoin('q.userAnswers', 'ua')
                ->addSelect('sa', 'ua');
        }

        return $builder->getQuery()->getOneOrNullResult();

    }

    //    /**
    //     * @return Survey[] Returns an array of Survey objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Survey
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
