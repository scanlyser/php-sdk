<?php

declare(strict_types=1);

namespace ScanLyser\Exceptions;

use RuntimeException;

class ScanLyserException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
