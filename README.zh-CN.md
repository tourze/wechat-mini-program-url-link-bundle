# wechat-mini-program-url-link-bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen.svg)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

微信小程序 URL Link 管理包，提供小程序短链接（URL Link）和推广码的生成、管理和统计功能。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置](#配置)
- [主要功能](#主要功能)
- [高级用法](#高级用法)
- [测试](#测试)
- [依赖包](#依赖包)
- [License](#license)

## 功能特性

- 生成微信小程序 URL Link（短链接）
- 推广码生成和管理
- 访问统计和分析
- 自动查询短链接访问状态
- 推广效果每日统计

## 安装

```bash
composer require tourze/wechat-mini-program-url-link-bundle
```

## 快速开始

### 1. 注册 Bundle

在您的 `config/bundles.php` 文件中：

```php
return [
    // ... 其他 bundles
    WechatMiniProgramUrlLinkBundle\WechatMiniProgramUrlLinkBundle::class => ['all' => true],
];
```

### 2. 更新数据库结构

```bash
php bin/console doctrine:schema:update --force
```

### 3. 基本用法

```php
// 生成推广码
$promotionCode = new PromotionCode();
$promotionCode->setName('新年促销');
$promotionCode->setCode('NY2024');
$promotionCode->setLinkUrl('pages/promotion/detail');
$promotionCode->setAccount($miniProgramAccount);

$entityManager->persist($promotionCode);
$entityManager->flush();

// 访问短链接
// 访问：https://yourdomain.com/t.htm?NY2024
```

## 配置

该包使用标准的 Symfony Bundle 配置方式，主要配置项通过服务容器注入。

### 必要的服务依赖

- `WechatMiniProgramBundle\Service\Client` - 微信小程序客户端服务
- `EntityManagerInterface` - Doctrine 实体管理器
- `LoggerInterface` - 日志服务

### 环境变量

```bash
# 重定向页面路径（可选，默认：pages/redirect/index）
WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH=pages/redirect/index
```

## 主要功能

### 1. URL Link（短链接）管理

URL Link 是微信小程序提供的短链接功能，可以在微信外部打开小程序。

#### 实体说明

- `UrlLink` - URL Link 实体，存储生成的短链接信息
- `VisitLog` - 访问日志，记录用户访问记录
- `DailyStatus` - 每日统计数据

#### 核心服务

- `UrlLinkService` - URL Link 核心服务，提供短链接的查询和状态更新功能

### 2. 推广码功能

推广码系统用于追踪不同渠道的推广效果。

#### 实体说明

- `PromotionCode` - 推广码实体
- `CodeRule` - 推广码规则配置
- `CodeRuleTag` - 推广码标签

### 3. API 接口

#### 生成短链接
- **路由**: `POST /api/wechat-mini-program/url-link/generate`
- **请求参数**: `GenerateUrlLinkRequest`
- **功能**: 生成新的小程序短链接

#### 查询短链接
- **路由**: `POST /api/wechat-mini-program/url-link/query`
- **请求参数**: `QueryUrlLinkRequest`
- **功能**: 查询短链接的访问状态

### 4. 控制台命令

#### wechat-mini-program:count-promotion-daily-status

定期统计推广码的访问数量。

**用途**: 
- 统计每个推广码的日访问量
- 更新每日统计数据
- 支持定时任务执行（每10分钟）

**使用方式**:
```bash
bin/console wechat-mini-program:count-promotion-daily-status
```

**功能说明**:
- 自动统计当天所有推广码的访问次数
- 更新或创建 `DailyStatus` 记录
- 仅在访问量增加时更新数据

#### wechat-mini-program:query-url-link-result

批量查询 URL Link 的点击结果。

**用途**:
- 批量检查未确认的短链接状态
- 通过微信API获取访问者信息
- 自动标记超时的链接

**使用方式**:
```bash
# 默认处理500条，60分钟超时
bin/console wechat-mini-program:query-url-link-result

# 自定义处理条数和超时时间
bin/console wechat-mini-program:query-url-link-result 1000 120
```

**参数说明**:
- `limit` (可选): 单次处理的记录数，默认 500
- `minute` (可选): 超时时间（分钟），默认 60

**功能说明**:
- 查询所有 `checked = false` 的 URL Link
- 调用微信API获取访问者的 OpenID
- 超过指定时间未访问的链接自动标记为已检查
- 支持定时任务执行（每10分钟）

### 5. 事件系统

#### PromotionCodeRequestEvent

推广码请求事件，在处理推广码相关请求时触发。

## 数据库表结构

包含以下主要数据表：

- `wechat_mini_program_url_link` - URL Link 表
- `wechat_mini_program_promotion_code` - 推广码表
- `wechat_mini_program_code_rule` - 推广码规则表
- `wechat_mini_program_code_rule_tag` - 推广码标签表
- `wechat_mini_program_visit_log` - 访问日志表
- `wechat_mini_program_daily_status` - 每日统计表

## 高级用法

### 生成 URL Link

```php
use WechatMiniProgramUrlLinkBundle\Request\GenerateUrlLinkRequest;

$request = new GenerateUrlLinkRequest();
$request->setPath('/pages/index/index');
$request->setQuery('source=test');
$request->setIsExpire(true);
$request->setExpireType(1);
$request->setExpireInterval(7);

// 通过 API 接口调用
// POST /api/wechat-mini-program/url-link/generate
```

### 查询 URL Link 状态

```php
use WechatMiniProgramUrlLinkBundle\Service\UrlLinkService;

// 注入服务
$urlLinkService = $container->get(UrlLinkService::class);

// 查询特定 URL Link 的状态
$urlLink = $urlLinkRepository->find($id);
$urlLinkService->apiCheck($urlLink);
```

### 为推广码生成二维码

```php
use WechatMiniProgramUrlLinkBundle\Service\PromotionCodeQrcodeService;

// 注入服务
$qrcodeService = $container->get(PromotionCodeQrcodeService::class);

// 为推广码生成二维码
$imageUrl = $qrcodeService->generateQrcode($promotionCode);
```

## 测试

运行单元测试：

```bash
./vendor/bin/phpunit packages/wechat-mini-program-url-link-bundle/tests
```

## 依赖包

- `tourze/wechat-mini-program-bundle` - 微信小程序基础功能包
- `tourze/doctrine-*` - Doctrine 扩展包系列
- `tourze/symfony-cron-job-bundle` - 定时任务支持
- Symfony 6.4+ 组件

## License

MIT