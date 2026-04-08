<?php

declare(strict_types=1);

namespace ScanLyser\Data;

use ScanLyser\Enums\IssueCategory;
use ScanLyser\Enums\IssueSeverity;

readonly class Issue
{
    /**
     * @param  array<int, string>  $culprits
     */
    public function __construct(
        public string $type,
        public IssueCategory $category,
        public IssueSeverity $severity,
        public string $message,
        public string $url,
        public array $culprits,
        public ?string $helpUrl,
    ) {}

    /**
     * Create from API response data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            type: $data['type'],
            category: IssueCategory::from($data['category']),
            severity: IssueSeverity::from($data['severity']),
            message: $data['message'],
            url: $data['url'],
            culprits: $data['culprits'] ?? [],
            helpUrl: $data['help_url'] ?? null,
        );
    }
}
