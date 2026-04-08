<?php

declare(strict_types=1);

namespace ScanLyser\Exceptions;

class ValidationException extends ScanLyserException
{
    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(
        string $message = 'Validation failed.',
        public readonly array $errors = [],
    ) {
        parent::__construct($message, 422);
    }
}
