<?php

declare(strict_types=1);

namespace ScanLyser\Data;

use ScanLyser\Enums\ScanStatus;

readonly class ScanPage
{
    /**
     * @param  array<int, Issue>|null  $issues
     */
    public function __construct(
        public string $id,
        public string $url,
        public ScanStatus $status,
        public int $issuesCount,
        public ?array $issues,
        public ?string $completedAt,
    ) {}

    /**
     * Create from API response data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
            url: $data['url'],
            status: ScanStatus::from($data['status']),
            issuesCount: $data['issues_count'],
            issues: isset($data['issues'])
                ? array_map(fn (array $issue) => Issue::from($issue), $data['issues'])
                : null,
            completedAt: $data['completed_at'] ?? null,
        );
    }
}
