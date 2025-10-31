<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use League\Flysystem\FilesystemOperator;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\String\Slugger\SluggerInterface;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramQrcodeLinkBundle\Request\CodeUnLimitRequest;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidRequestParameterException;

#[WithMonologChannel(channel: 'wechat_mini_program')]
#[Autoconfigure(public: true)]
readonly class PromotionCodeQrcodeService
{
    public function __construct(
        private Client $client,
        private FilesystemOperator $filesystem,
        private SluggerInterface $slugger,
        private LoggerInterface $logger,
        private PageService $pageService,
    ) {
    }

    /**
     * 生成小程序码并保存
     */
    public function generateQrcode(PromotionCode $promotionCode): ?string
    {
        try {
            // 准备请求参数
            $request = new CodeUnLimitRequest();
            $account = $promotionCode->getAccount();
            if (null === $account) {
                throw new InvalidRequestParameterException('推广码未配置账号');
            }
            $request->setAccount($account);
            $request->setScene($promotionCode->getCode());

            // 设置小程序页面路径为重定向页面
            // 前端会调用 GetWechatMiniProgramPromotionCodeInfo 来获取最终跳转地址
            $request->setPage($this->pageService->getRedirectPage());

            // 设置环境版本
            if (null !== $promotionCode->getEnvVersion()) {
                $request->setEnvVersion($promotionCode->getEnvVersion()->value);
            }

            // 设置其他参数
            $request->setCheckPath(false); // 允许未发布的页面
            $request->setWidth(430); // 二维码宽度
            $request->setAutoColor(false); // 不自动配色
            $request->setHyaline(false); // 不透明背景

            // 调用微信API生成小程序码
            $imageContent = $this->client->request($request);

            if (!is_string($imageContent) || '' === $imageContent) {
                throw new InvalidRequestParameterException('生成的小程序码内容为空');
            }

            // 保存图片文件
            return $this->saveQrcodeImage($promotionCode, $imageContent);
        } catch (\Exception $e) {
            $this->logger->error('生成小程序码失败', [
                'promotion_code_id' => $promotionCode->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * 保存小程序码图片
     */
    private function saveQrcodeImage(PromotionCode $promotionCode, string $imageContent): string
    {
        // 生成文件名
        $safeFilename = $this->slugger->slug($promotionCode->getName() ?? 'qrcode');
        $filename = sprintf('%s-%s.png', $safeFilename, $promotionCode->getCode());

        // 创建年月目录结构
        $dateDirectory = date('Y/m');
        $relativePath = sprintf('promotion-qrcode/%s/%s', $dateDirectory, $filename);

        // 使用 Flysystem 保存文件
        $this->filesystem->write($relativePath, $imageContent);

        // 返回相对路径，供存储到数据库
        return $relativePath;
    }

    /**
     * 删除小程序码图片
     */
    public function deleteQrcode(PromotionCode $promotionCode): void
    {
        $imageUrl = $promotionCode->getImageUrl();
        if (null === $imageUrl) {
            return;
        }

        try {
            // imageUrl 本身就是相对路径
            if ($this->filesystem->fileExists($imageUrl)) {
                $this->filesystem->delete($imageUrl);
            }
        } catch (\Exception $e) {
            $this->logger->warning('删除小程序码图片失败', [
                'promotion_code_id' => $promotionCode->getId(),
                'image_url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
