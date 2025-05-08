<?php

namespace WechatMiniProgramUrlLinkBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

/**
 * @method PromotionCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromotionCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromotionCode[]    findAll()
 * @method PromotionCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionCodeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromotionCode::class);
    }
}
