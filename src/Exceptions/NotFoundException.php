<?php

declare(strict_types=1);

namespace ScanLyser\Exceptions;

class NotFoundException extends ScanLyserException
{
    public function __construct(string $message = 'Resource not found.')
    {
        parent::__construct($message, 404);
    }
}
