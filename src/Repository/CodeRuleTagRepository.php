<?php

namespace WechatMiniProgramUrlLinkBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRuleTag;

/**
 * @method CodeRuleTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeRuleTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeRuleTag[]    findAll()
 * @method CodeRuleTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRuleTagRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeRuleTag::class);
    }
}
