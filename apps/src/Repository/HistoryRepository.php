<?php

namespace Labstag\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Labstag\Entity\History;
use Labstag\Entity\User;
use Labstag\Lib\ServiceEntityRepositoryLib;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepositoryLib
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * @return QueryBuilder|void
     */
    public function findAllActiveByUser(?User $user)
    {
        if (is_null($user)) {
            return;
        }

        $dql = $this->createQueryBuilder('h');
        $dql->innerJoin('h.refuser', 'u');
        $dql->where('h.enable = :enable');
        $dql->andWhere('b.createdAt<=now()');
        $dql->andWhere('u.id = :iduser');
        $dql->orderBy('h.createdAt', 'DESC');
        $dql->setParameters(
            [
                'iduser' => $user->getId(),
                'enable' => true,
            ]
        );

        return $dql;
    }

    public function findAllActive(array $context = array()): QueryBuilder
    {
        $dql = $this->createQueryBuilder('h');
        $dql->where('h.createdAt<=now()');
        $this->setArgs('h', History::class, $context, $dql);
        $dql->orderBy('h.createdAt', 'DESC');

        return $dql;
    }
}
