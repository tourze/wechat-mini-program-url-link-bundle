<?php

namespace WechatMiniProgramUrlLinkBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;
use WechatMiniProgramUrlLinkBundle\Repository\DailyStatusRepository;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;
use WechatMiniProgramUrlLinkBundle\Repository\VisitLogRepository;

#[AsCronTask('*/10 * * * *')]
#[AsCommand(name: self::NAME, description: '定期统计推广码的访问数量')]
class CountPromotionDailyStatusCommand extends Command
{
    public const NAME = 'wechat-mini-program:count-promotion-daily-status';
    
public function __construct(
        private readonly VisitLogRepository $visitLogRepository,
        private readonly PromotionCodeRepository $codeRepository,
        private readonly DailyStatusRepository $dailyStatusRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = CarbonImmutable::now()->startOfDay();
        $list = $this->visitLogRepository->createQueryBuilder('v')
            ->select('count(v.id) as total, identity(v.code) as code')
            ->where('v.createTime between :start and :end')
            ->setParameter('start', CarbonImmutable::today()->startOfDay())
            ->setParameter('end', CarbonImmutable::today()->endOfDay())
            ->groupBy('v.code')
            ->getQuery()
            ->getResult();

        foreach ($list as $value) {
            // $output->writeln("更新统计，{$value['code']}, {$value['total']}");
            $code = $this->codeRepository->find($value['code']);
            if (null === $code) {
                continue;
            }
            $status = $this->dailyStatusRepository->findOneBy([
                'code' => $code,
                'date' => $date,
            ]);
            if (null === $status) {
                $status = new DailyStatus();
                $status->setCode($code);
                $status->setDate($date);
            }
            if ($value['total'] > $status->getTotal()) {
                $status->setTotal($value['total']);
                $this->entityManager->persist($status);
                $this->entityManager->flush();
                $output->writeln("更新统计，{$code->getId()}, {$status->getTotal()}");
            }
        }

        return Command::SUCCESS;
    }
}
