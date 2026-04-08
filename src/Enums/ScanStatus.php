<?php

declare(strict_types=1);

namespace ScanLyser\Enums;

enum ScanStatus: string
{
    case Pending = 'pending';
    case Crawling = 'crawling';
    case Analysing = 'analysing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Rescanning = 'rescanning';
}
