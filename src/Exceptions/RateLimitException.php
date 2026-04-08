<?php

declare(strict_types=1);

namespace ScanLyser\Exceptions;

class RateLimitException extends ScanLyserException
{
    public function __construct(string $message = 'Rate limit exceeded.')
    {
        parent::__construct($message, 429);
    }
}
