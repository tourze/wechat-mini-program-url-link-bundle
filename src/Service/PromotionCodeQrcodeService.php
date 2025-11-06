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
        // 检查是否已存在相同的二维码文件（幂等性）
        $existingPath = $this->findExistingQrcode($promotionCode);
        if ($existingPath !== null) {
            $this->logger->info('二维码文件已存在，跳过重新生成', [
                'promotion_code_id' => $promotionCode->getId(),
                'existing_path' => $existingPath,
            ]);
            return $existingPath;
        }

        // 生成文件名
        $safeFilename = $this->slugger->slug($promotionCode->getName() ?? 'qrcode');
        $filename = sprintf('%s-%s.png', $safeFilename, $promotionCode->getCode());

        // 创建年月目录结构
        $dateDirectory = date('Y/m');
        $relativePath = sprintf('promotion-qrcode/%s/%s', $dateDirectory, $filename);

        // 添加重试机制保存文件
        $maxRetries = 3;
        $retryDelay = 100; // 毫秒

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // 使用 Flysystem 保存文件
                $this->filesystem->write($relativePath, $imageContent);

                // 验证文件是否成功写入
                if (!$this->filesystem->fileExists($relativePath)) {
                    throw new \RuntimeException('文件写入后验证失败');
                }

                // 验证文件内容是否正确
                $savedContent = $this->filesystem->read($relativePath);
                if ($savedContent !== $imageContent) {
                    throw new \RuntimeException('文件内容验证失败');
                }

                $this->logger->info('小程序码保存成功', [
                    'promotion_code_id' => $promotionCode->getId(),
                    'path' => $relativePath,
                    'attempt' => $attempt,
                ]);

                break;
            } catch (\Exception $e) {
                $this->logger->warning('保存小程序码失败，尝试重试', [
                    'promotion_code_id' => $promotionCode->getId(),
                    'path' => $relativePath,
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries,
                    'error' => $e->getMessage(),
                ]);

                if ($attempt === $maxRetries) {
                    throw new \RuntimeException("保存小程序码失败，已重试{$maxRetries}次: " . $e->getMessage(), 0, $e);
                }

                // 简单的延迟重试
                usleep($retryDelay * 1000 * $attempt);
            }
        }

        // 返回相对路径，供存储到数据库
        return $relativePath;
    }

    /**
     * 查找已存在的二维码文件（实现幂等性）
     */
    private function findExistingQrcode(PromotionCode $promotionCode): ?string
    {
        // 生成可能的文件名格式进行查找
        $safeFilename = $this->slugger->slug($promotionCode->getName() ?? 'qrcode');
        $basePattern = sprintf('%s-%s', $safeFilename, $promotionCode->getCode());

        // 检查当前月份和上个月的目录
        $directories = [
            date('Y/m'),
            date('Y/m', strtotime('-1 month')),
        ];

        foreach ($directories as $dateDirectory) {
            $basePath = sprintf('promotion-qrcode/%s/', $dateDirectory);

            try {
                if (!$this->filesystem->directoryExists($basePath)) {
                    continue;
                }

                $files = $this->filesystem->listContents($basePath)->filter(function ($file) use ($basePattern, $basePath) {
                    return $file->isFile() &&
                           str_starts_with($file->path(), $basePath . $basePattern) &&
                           str_ends_with($file->path(), '.png');
                });

                foreach ($files as $file) {
                    // 找到第一个匹配的文件就返回
                    return $file->path();
                }
            } catch (\Exception $e) {
                $this->logger->debug('搜索已存在二维码文件时出错', [
                    'directory' => $basePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
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
