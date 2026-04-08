<?php

declare(strict_types=1);

namespace ScanLyser\Resources;

use ScanLyser\Data\PaginatedResponse;
use ScanLyser\Data\ScanPage;
use ScanLyser\Client;

readonly class PageResource
{
    public function __construct(
        private Client $client,
        private string $teamId,
    ) {}

    /**
     * List pages for a scan.
     *
     * @return PaginatedResponse<ScanPage>
     */
    public function list(string $scanId, int $perPage = 15): PaginatedResponse
    {
        $response = $this->client->get("{$this->teamId}/scans/{$scanId}/pages", ['per_page' => $perPage]);

        return PaginatedResponse::from($response, fn (array $data) => ScanPage::from($data));
    }

    /**
     * Get a single page with its issues.
     */
    public function get(string $scanId, string $pageId): ScanPage
    {
        $response = $this->client->get("{$this->teamId}/scans/{$scanId}/pages/{$pageId}");

        return ScanPage::from($response['data']);
    }
}
