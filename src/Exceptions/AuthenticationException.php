<?php

declare(strict_types=1);

namespace ScanLyser\Exceptions;

class AuthenticationException extends ScanLyserException
{
    public function __construct(string $message = 'Authentication failed.')
    {
        parent::__construct($message, 401);
    }
}
