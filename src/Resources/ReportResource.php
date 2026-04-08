<?php

declare(strict_types=1);

namespace ScanLyser\Resources;

use ScanLyser\Client;

readonly class ReportResource
{
    public function __construct(
        private Client $client,
        private string $teamId,
    ) {}

    /**
     * Get the scan report as a decoded JSON array.
     *
     * @return array<string, mixed>
     */
    public function json(string $scanId): array
    {
        return $this->client->get("{$this->teamId}/scans/{$scanId}/report", ['format' => 'json']);
    }

    /**
     * Download the scan report as a PDF and save it to disk.
     */
    public function pdf(string $scanId, string $saveTo): void
    {
        $content = $this->client->getRaw("{$this->teamId}/scans/{$scanId}/report");

        file_put_contents($saveTo, $content);
    }
}
