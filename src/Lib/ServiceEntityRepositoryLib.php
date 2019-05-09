<?php

namespace Labstag\Lib;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class ServiceEntityRepositoryLib extends ServiceEntityRepository
{
    public function findDataInTrash()
    {
        $em = $this->getEntityManager();
        $em->getFilters()->disable('softdeleteable');
        $dql = $em->createQueryBuilder();
        $dql->select('e');
        $dql->from($this->_entityName, 'e');
        $dql->where('e.deletedAt IS NOT NULL');
        $query = $dql->getQuery();

        return $query->getResult();
    }

    public function findOneDateInTrash($id)
    {
        $em = $this->getEntityManager();
        $em->getFilters()->disable('softdeleteable');
        $dql = $em->createQueryBuilder();
        $dql->select('e');
        $dql->from($this->_entityName, 'e');
        $dql->where('e.deletedAt IS NOT NULL AND e.id=:id');
        $dql->setParameter('id', $id);
        $query = $dql->getQuery();

        return $query->getOneOrNullResult();
    }
}
