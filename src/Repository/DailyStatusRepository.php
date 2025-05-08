<?php

namespace WechatMiniProgramUrlLinkBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;

/**
 * @method DailyStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyStatus[]    findAll()
 * @method DailyStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyStatusRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyStatus::class);
    }
}
