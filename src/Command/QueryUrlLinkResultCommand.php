<?php

namespace WechatMiniProgramUrlLinkBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Repository\UrlLinkRepository;
use WechatMiniProgramUrlLinkBundle\Service\UrlLinkService;

#[AsCronTask(expression: '*/10 * * * *')]
#[AsCommand(name: self::NAME, description: '批量查询UrlLink的点击结果')]
class QueryUrlLinkResultCommand extends Command
{
    public const NAME = 'wechat-mini-program:query-url-link-result';
    
public function __construct(
        private readonly UrlLinkRepository $linkRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlLinkService $urlLinkService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('limit', InputArgument::OPTIONAL, '处理条数', '500');
        $this->addArgument('minute', InputArgument::OPTIONAL, '多少分钟内没访问，就当作无效', '60');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $urlLinks = $this->linkRepository->createQueryBuilder('a')
            ->where('a.checked = false')
            ->orderBy('a.id', Criteria::ASC)
            ->setMaxResults(intval($input->getArgument('limit'))) // 单次只处理部分数据
            ->getQuery()
            ->toIterable();
        foreach ($urlLinks as $urlLink) {
            $diff = CarbonImmutable::now()->diffInMinutes($urlLink->getCreateTime());
            $diff = abs($diff);
            if ($diff > $input->getArgument('minute')) {
                $output->writeln("短链{$urlLink->getId()}已超时{$diff}分钟，不再检查了");
                $urlLink->setChecked(true);
                $this->entityManager->persist($urlLink);
                $this->entityManager->flush();
                $this->entityManager->detach($urlLink);
            }

            /* @var UrlLink $urlLink */
            $output->writeln("正在处理短链：{$urlLink->getId()}");

            $this->urlLinkService->apiCheck($urlLink);
            $this->entityManager->detach($urlLink);
        }

        return Command::SUCCESS;
    }
}
