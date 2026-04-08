<?php

declare(strict_types=1);

namespace ScanLyser\Enums;

enum IssueSeverity: string
{
    case Critical = 'critical';
    case Major = 'major';
    case Minor = 'minor';
    case Info = 'info';
}
