<?php

declare(strict_types=1);

namespace WechatMiniProgramUrlLinkBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetWechatMiniProgramPromotionCodeInfo Procedure 的参数对象
 *
 * 用于获取小程序推广码配置信息
 */
readonly class GetWechatMiniProgramPromotionCodeInfoParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '码ID')]
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $id,
    ) {
    }
}
