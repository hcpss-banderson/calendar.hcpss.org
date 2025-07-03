<?php

namespace App\Repository;

use App\Entity\Calendar;
use App\Entity\Event;
use App\Entity\Occurrence;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Occurrence>
 *
 * @method Occurrence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Occurrence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Occurrence[]    findAll()
 * @method Occurrence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OccurrenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Occurrence::class);
    }

    public function deleteAll() : void
    {
        $entities = $this->findAll();
        foreach ($entities as $entity) {
            $this->remove($entity);
        }
    }

    public function add(Occurrence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Occurrence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param Calendar[]|null $calendars
     * @return array
     */
    public function findBetweenDates(DateTimeImmutable $start, DateTimeImmutable $end, ?array $calendars): array
    {
        $builder = $this->createQueryBuilder('o')
            ->andWhere('o.start >= :start')
            ->andWhere('o.end <= :end');

        if ($calendars) {
            $builder
                ->join(Event::class, 'e', Join::WITH, 'o.event = e.id')
                ->andWhere('e.calendar IN (:calendar_ids)')
                ->setParameter(':calendar_ids', array_map(function (Calendar $calendar) {
                    return $calendar->getId();
                }, $calendars));
        }

        $builder
            ->setParameter(':start', $start)
            ->setParameter(':end', $end)
            ->orderBy('o.start');

        return $builder->getQuery()->getResult();
    }

//    /**
//     * @return Occurrence[] Returns an array of Occurrence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Occurrence
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
