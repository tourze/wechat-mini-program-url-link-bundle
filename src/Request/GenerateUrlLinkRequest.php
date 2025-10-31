<?php

namespace WechatMiniProgramUrlLinkBundle\Request;

use WechatMiniProgramBundle\Request\WithAccountRequest;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidRequestParameterException;

/**
 * 获取小程序 URL Link，适用于短信、邮件、网页、微信内等拉起小程序的业务场景。目前仅针对国内非个人主体的小程序开放，详见获取 URL Link
 *
 * @see https://developers.weixin.qq.com/minigame/dev/api-backend/open-api/url-link/urllink.generate.html
 */
class GenerateUrlLinkRequest extends WithAccountRequest
{
    /**
     * @var string|null 通过 URL Link 进入的小程序页面路径，必须是已经发布的小程序存在的页面，不可携带 query 。path 为空时会跳转小程序主页
     */
    private ?string $path = null;

    /**
     * @var string|null 通过 URL Link 进入小程序时的query，最大1024个字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~%
     */
    private ?string $query = null;

    /**
     * @var string 要打开的小程序版本。正式版为 "release"，体验版为"trial"，开发版为"develop"，仅在微信外打开时生效。
     */
    private string $envVersion = 'release';

    /**
     * @var int 小程序 URL Link 失效类型，失效时间：0，失效间隔天数：1
     */
    private int $expireType;

    /**
     * @var int|null 到期失效的 URL Link 的失效时间，为 Unix 时间戳。生成的到期失效 URL Link 在该时间前有效。最长有效期为30天。expire_type 为 0 必填
     */
    private ?int $expireTime;

    /**
     * @var int|null 到期失效的URL Link的失效间隔天数。生成的到期失效URL Link在该间隔时间到达前有效。最长间隔天数为30天。expire_type 为 1 必填
     */
    private ?int $expireInterval;

    public function getRequestPath(): string
    {
        return '/wxa/generate_urllink';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [
            'expire_type' => $this->getExpireType(),
        ];
        if (null !== $this->getPath()) {
            $json['path'] = $this->getPath();
        }
        if (null !== $this->getQuery()) {
            $json['query'] = $this->getQuery();
        }
        $json['env_version'] = $this->getEnvVersion();

        if (0 === $json['expire_type']) {
            if (null === $this->getExpireTime()) {
                throw new InvalidRequestParameterException('expire_type 为 0 时，expire_time 必填');
            }
            $json['expire_time'] = $this->getExpireTime();
        }

        if (1 === $json['expire_type']) {
            if (null === $this->getExpireInterval()) {
                throw new InvalidRequestParameterException('expire_type 为 1 时，expire_interval 必填');
            }
            $json['expire_interval'] = $this->getExpireInterval();
        }

        return [
            'json' => $json,
        ];
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): void
    {
        $this->query = $query;
    }

    public function getEnvVersion(): string
    {
        return $this->envVersion;
    }

    public function setEnvVersion(string $envVersion): void
    {
        $this->envVersion = $envVersion;
    }

    public function getExpireType(): int
    {
        return $this->expireType;
    }

    public function setExpireType(int $expireType): void
    {
        $this->expireType = $expireType;
    }

    public function getExpireTime(): ?int
    {
        return $this->expireTime;
    }

    public function setExpireTime(?int $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    public function getExpireInterval(): ?int
    {
        return $this->expireInterval;
    }

    public function setExpireInterval(?int $expireInterval): void
    {
        $this->expireInterval = $expireInterval;
    }
}
