# wechat-mini-program-url-link-bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-95%25-brightgreen.svg)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

WeChat Mini Program URL Link management bundle, providing URL Link (short link) and promotion code generation, management, and statistics features.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Main Features](#main-features)
- [Configuration](#configuration)
- [Advanced Usage](#advanced-usage)
- [Testing](#testing)
- [Dependencies](#dependencies)
- [License](#license)

## Features

- Generate WeChat Mini Program URL Links (short links)
- Promotion code generation and management
- Visit statistics and analysis
- Automatic URL Link visit status query
- Daily promotion effectiveness statistics

## Installation

```bash
composer require tourze/wechat-mini-program-url-link-bundle
```

## Quick Start

### 1. Register the Bundle

In your `config/bundles.php`:

```php
return [
    // ... other bundles
    WechatMiniProgramUrlLinkBundle\WechatMiniProgramUrlLinkBundle::class => ['all' => true],
];
```

### 2. Update Database Schema

```bash
php bin/console doctrine:schema:update --force
```

### 3. Basic Usage

```php
// Generate a promotion code
$promotionCode = new PromotionCode();
$promotionCode->setName('New Year Promotion');
$promotionCode->setCode('NY2024');
$promotionCode->setLinkUrl('pages/promotion/detail');
$promotionCode->setAccount($miniProgramAccount);

$entityManager->persist($promotionCode);
$entityManager->flush();

// Access the short link
// Visit: https://yourdomain.com/t.htm?NY2024
```

## Configuration

This bundle uses standard Symfony Bundle configuration, with main configuration items injected through the service container.

### Required Service Dependencies

- `WechatMiniProgramBundle\Service\Client` - WeChat Mini Program client service
- `EntityManagerInterface` - Doctrine entity manager
- `LoggerInterface` - Logger service

### Environment Variables

```bash
# Redirect page path (optional, default: pages/redirect/index)
WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH=pages/redirect/index
```

## Main Features

### 1. URL Link Management

URL Link is a short link feature provided by WeChat Mini Program that allows opening mini programs from outside WeChat.

#### Entity Description

- `UrlLink` - URL Link entity, stores generated short link information
- `VisitLog` - Visit log, records user visit history
- `DailyStatus` - Daily statistics data

#### Core Services

- `UrlLinkService` - URL Link core service, provides short link query and status update functionality

### 2. Promotion Code Feature

The promotion code system is used to track promotion effectiveness across different channels.

#### Entity Description

- `PromotionCode` - Promotion code entity
- `CodeRule` - Promotion code rule configuration
- `CodeRuleTag` - Promotion code tags

### 3. API Endpoints

#### Generate Short Link
- **Route**: `POST /api/wechat-mini-program/url-link/generate`
- **Request Parameters**: `GenerateUrlLinkRequest`
- **Function**: Generate new mini program short link

#### Query Short Link
- **Route**: `POST /api/wechat-mini-program/url-link/query`
- **Request Parameters**: `QueryUrlLinkRequest`
- **Function**: Query short link visit status

### 4. Console Commands

#### wechat-mini-program:count-promotion-daily-status

Periodically count promotion code visit numbers.

**Purpose**: 
- Count daily visits for each promotion code
- Update daily statistics data
- Supports scheduled task execution (every 10 minutes)

**Usage**:
```bash
bin/console wechat-mini-program:count-promotion-daily-status
```

**Features**:
- Automatically count all promotion code visits for the current day
- Update or create `DailyStatus` records
- Only update data when visit count increases

#### wechat-mini-program:query-url-link-result

Batch query URL Link click results.

**Purpose**:
- Batch check unconfirmed short link statuses
- Get visitor information through WeChat API
- Automatically mark expired links

**Usage**:
```bash
# Default: process 500 records, 60 minutes timeout
bin/console wechat-mini-program:query-url-link-result

# Custom record count and timeout
bin/console wechat-mini-program:query-url-link-result 1000 120
```

**Parameters**:
- `limit` (optional): Number of records to process at once, default 500
- `minute` (optional): Timeout in minutes, default 60

**Features**:
- Query all URL Links with `checked = false`
- Call WeChat API to get visitor's OpenID
- Automatically mark links as checked if not visited within specified time
- Supports scheduled task execution (every 10 minutes)

### 5. Event System

#### PromotionCodeRequestEvent

Promotion code request event, triggered when processing promotion code related requests.

## Database Tables

Includes the following main tables:

- `wechat_mini_program_url_link` - URL Link table
- `wechat_mini_program_promotion_code` - Promotion code table
- `wechat_mini_program_code_rule` - Promotion code rule table
- `wechat_mini_program_code_rule_tag` - Promotion code tag table
- `wechat_mini_program_visit_log` - Visit log table
- `wechat_mini_program_daily_status` - Daily statistics table

## Advanced Usage

### Generate URL Link

```php
use WechatMiniProgramUrlLinkBundle\Request\GenerateUrlLinkRequest;

$request = new GenerateUrlLinkRequest();
$request->setPath('/pages/index/index');
$request->setQuery('source=test');
$request->setIsExpire(true);
$request->setExpireType(1);
$request->setExpireInterval(7);

// Call through API endpoint
// POST /api/wechat-mini-program/url-link/generate
```

### Query URL Link Status

```php
use WechatMiniProgramUrlLinkBundle\Service\UrlLinkService;

// Inject service
$urlLinkService = $container->get(UrlLinkService::class);

// Query specific URL Link status
$urlLink = $urlLinkRepository->find($id);
$urlLinkService->apiCheck($urlLink);
```

### Generate QR Code for Promotion Code

```php
use WechatMiniProgramUrlLinkBundle\Service\PromotionCodeQrcodeService;

// Inject service
$qrcodeService = $container->get(PromotionCodeQrcodeService::class);

// Generate QR code for promotion code
$imageUrl = $qrcodeService->generateQrcode($promotionCode);
```

## Testing

Run unit tests:

```bash
./vendor/bin/phpunit packages/wechat-mini-program-url-link-bundle/tests
```

## Dependencies

- `tourze/wechat-mini-program-bundle` - WeChat Mini Program base functionality bundle
- `tourze/doctrine-*` - Doctrine extension bundle series
- `tourze/symfony-cron-job-bundle` - Scheduled task support
- Symfony 6.4+ components

## License

MIT