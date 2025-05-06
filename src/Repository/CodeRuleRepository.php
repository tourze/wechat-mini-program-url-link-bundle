<?php

namespace WechatMiniProgramUrlLinkBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRule;

/**
 * @method CodeRule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeRule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeRule[]    findAll()
 * @method CodeRule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRuleRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeRule::class);
    }
}
