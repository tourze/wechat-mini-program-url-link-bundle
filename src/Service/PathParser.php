<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Psr\Link\LinkInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use WechatMiniProgramBundle\Service\PathParserInterface;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

#[AsDecorator(decorates: PathParserInterface::class, priority: 99)]
class PathParser implements PathParserInterface
{
    public function __construct(
        #[AutowireDecorated] private readonly PathParserInterface $inner,
        private readonly PromotionCodeRepository $promotionCodeRepository,
    )
    {
    }

    public function parsePath(string $path, array $query = []): LinkInterface
    {
        // 后台活动码的取参逻辑
        if ('pages/redirect/index' === trim($path, '/') && (isset($query['scene']) || isset($query['id']))) {
            $code = null;
            if (isset($query['scene'])) {
                $code = $this->promotionCodeRepository->findOneBy([
                    'id' => $query['scene'],
                    'valid' => true,
                ]);
            }

            if (null === $code && isset($query['id'])) {
                $code = $this->promotionCodeRepository->findOneBy([
                    'id' => $query['id'],
                    'valid' => true,
                ]);
            }
            // 目前肯定是整形的
            if (null !== $code) {
                // 解析和判断是否有s参数，有的话就直接取用
                $tmp = parse_url($code->getLinkUrl());
                $_query = $tmp['query'] ?? '';
                parse_str($_query, $query);
                $path = $tmp['path'];
            }
        }

        return $this->inner->parsePath($path, $query);
    }
}
