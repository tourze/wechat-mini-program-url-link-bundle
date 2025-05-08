<?php

namespace WechatMiniProgramUrlLinkBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;

/**
 * @method UrlLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method UrlLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method UrlLink[]    findAll()
 * @method UrlLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlLinkRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrlLink::class);
    }
}
