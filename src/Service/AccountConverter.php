<?php

declare(strict_types=1);

namespace WechatMiniProgramUrlLinkBundle\Service;

use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Entity\Account;

/**
 * Account类型转换器和验证器
 *
 * 提供MiniProgramInterface到具体Account类的转换和验证功能
 * 确保接口抽象化的同时保持类型安全
 */
class AccountConverter
{
    /**
     * 将MiniProgramInterface转换为Account对象
     *
     * @param MiniProgramInterface $miniProgram 小程序接口实例
     * @throws \InvalidArgumentException 当传入的对象不是Account实例时
     */
    public function toAccount(MiniProgramInterface $miniProgram): Account
    {
        if ($miniProgram instanceof Account) {
            return $miniProgram;
        }

        throw new \InvalidArgumentException(sprintf(
            'Expected instance of %s, got %s',
            Account::class,
            get_class($miniProgram)
        ));
    }

    /**
     * 验证MiniProgramInterface是否为Account实例
     *
     * @param MiniProgramInterface|null $miniProgram 小程序接口实例
     * @return bool 是否为有效的Account实例
     */
    public function isValidAccount(?MiniProgramInterface $miniProgram): bool
    {
        return $miniProgram instanceof Account;
    }

    /**
     * 安全地将MiniProgramInterface转换为Account
     *
     * @param MiniProgramInterface|null $miniProgram 小程序接口实例
     * @return Account|null 转换后的Account对象，如果转换失败返回null
     */
    public function convertToAccount(?MiniProgramInterface $miniProgram): ?Account
    {
        if ($miniProgram === null) {
            return null;
        }

        return $this->isValidAccount($miniProgram) ? $this->toAccount($miniProgram) : null;
    }

    /**
     * 获取Account的额外验证信息
     *
     * @param MiniProgramInterface $miniProgram 小程序接口实例
     * @return array<string, mixed> 验证结果信息
     */
    public function getValidationInfo(MiniProgramInterface $miniProgram): array
    {
        return [
            'isAccount' => $this->isValidAccount($miniProgram),
            'class' => get_class($miniProgram),
            'appId' => $miniProgram->getAppId(),
            'supportsClient' => $this->isValidAccount($miniProgram), // 只有Account支持Client调用
        ];
    }
}