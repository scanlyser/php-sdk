<?php

declare(strict_types=1);

namespace ScanLyser\Exceptions;

class ForbiddenException extends ScanLyserException
{
    public function __construct(string $message = 'Access denied.')
    {
        parent::__construct($message, 403);
    }
}
