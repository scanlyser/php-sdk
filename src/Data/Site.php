<?php

declare(strict_types=1);

namespace ScanLyser\Data;

readonly class Site
{
    public function __construct(
        public string $id,
        public string $name,
        public string $url,
        public ?Scan $latestScan,
        public ?int $scansCount,
        public string $createdAt,
        public string $updatedAt,
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
            name: $data['name'],
            url: $data['url'],
            latestScan: isset($data['latest_scan']) ? Scan::from($data['latest_scan']) : null,
            scansCount: $data['scans_count'] ?? null,
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
        );
    }
}
