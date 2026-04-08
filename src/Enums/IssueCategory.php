<?php

declare(strict_types=1);

namespace ScanLyser\Enums;

enum IssueCategory: string
{
    case Wcag = 'wcag';
    case Seo = 'seo';
    case Performance = 'performance';
    case Ux = 'ux';
    case Sitewide = 'sitewide';
    case Other = 'other';
}
