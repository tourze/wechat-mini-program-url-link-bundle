<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Psr\Link\LinkInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use WechatMiniProgramBundle\Service\PathParserInterface;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

#[AsDecorator(decorates: PathParserInterface::class, priority: 99)]
class PathParser implements PathParserInterface
{
    public function __construct(
        #[AutowireDecorated] private readonly PathParserInterface $inner,
        private readonly PromotionCodeRepository $promotionCodeRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     */
    public function parsePath(string $path, array $query = []): LinkInterface
    {
        if (!$this->isRedirectPageWithPromotionParams($path, $query)) {
            return $this->inner->parsePath($path, $this->convertQueryKeys($query));
        }

        $code = $this->findPromotionCode($query);
        if (null === $code) {
            return $this->inner->parsePath($path, $this->convertQueryKeys($query));
        }

        $linkUrl = $code->getLinkUrl();
        if (null === $linkUrl) {
            return $this->inner->parsePath($path, $this->convertQueryKeys($query));
        }

        $parsedResult = $this->parsePromotionLinkUrl($linkUrl);
        if (null === $parsedResult) {
            return $this->inner->parsePath($path, $this->convertQueryKeys($query));
        }

        return $this->inner->parsePath($parsedResult['path'], $parsedResult['query']);
    }

    /**
     * @param array<string, mixed> $query
     */
    private function isRedirectPageWithPromotionParams(string $path, array $query): bool
    {
        return 'pages/redirect/index' === trim($path, '/')
            && (isset($query['scene']) || isset($query['id']));
    }

    /**
     * @param array<string, mixed> $query
     */
    private function findPromotionCode(array $query): ?PromotionCode
    {
        if (isset($query['scene'])) {
            $code = $this->promotionCodeRepository->findOneBy([
                'id' => $query['scene'],
                'valid' => true,
            ]);
            if (null !== $code) {
                return $code;
            }
        }

        if (isset($query['id'])) {
            return $this->promotionCodeRepository->findOneBy([
                'id' => $query['id'],
                'valid' => true,
            ]);
        }

        return null;
    }

    /**
     * @return array{path: string, query: array<string, mixed>}|null
     */
    private function parsePromotionLinkUrl(string $linkUrl): ?array
    {
        $tmp = parse_url($linkUrl);
        if (false === $tmp || !isset($tmp['path'])) {
            return null;
        }

        $queryString = $tmp['query'] ?? '';
        $parsedQuery = [];
        parse_str($queryString, $parsedQuery);

        // 确保$parsedQuery是正确的类型
        $convertedQuery = [];
        foreach ($parsedQuery as $key => $value) {
            $convertedQuery[(string) $key] = $value;
        }

        return [
            'path' => $tmp['path'],
            'query' => $convertedQuery,
        ];
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    private function convertQueryKeys(array $query): array
    {
        $convertedQuery = [];
        foreach ($query as $key => $value) {
            $convertedQuery[(string) $key] = $value;
        }

        return $convertedQuery;
    }
}
