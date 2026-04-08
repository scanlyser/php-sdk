<?php

declare(strict_types=1);

namespace ScanLyser\Data;

use ScanLyser\Enums\ScanStatus;
use ScanLyser\Enums\WcagLevel;

readonly class Scan
{
    public function __construct(
        public string $id,
        public string $siteId,
        public ScanStatus $status,
        public WcagLevel $wcagLevel,
        public int $pagesCrawled,
        public int $pagesTotal,
        public int $issuesCount,
        public ?ScanScores $scores,
        public string $createdAt,
        public ?string $completedAt,
        public ?string $failedAt,
        public ?string $failureReason,
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
            siteId: $data['site_id'],
            status: ScanStatus::from($data['status']),
            wcagLevel: WcagLevel::from($data['wcag_level']),
            pagesCrawled: $data['pages_crawled'],
            pagesTotal: $data['pages_total'],
            issuesCount: $data['issues_count'],
            scores: isset($data['scores']) ? ScanScores::from($data['scores']) : null,
            createdAt: $data['created_at'],
            completedAt: $data['completed_at'] ?? null,
            failedAt: $data['failed_at'] ?? null,
            failureReason: $data['failure_reason'] ?? null,
        );
    }

    /**
     * Check if the scan has finished (completed or failed).
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, [ScanStatus::Completed, ScanStatus::Failed]);
    }
}
