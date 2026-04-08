<?php

declare(strict_types=1);

namespace ScanLyser\Resources;

use ScanLyser\Data\Issue;
use ScanLyser\Data\PaginatedResponse;
use ScanLyser\Client;

readonly class IssueResource
{
    public function __construct(
        private Client $client,
        private string $teamId,
    ) {}

    /**
     * List issues for a scan, optionally filtered by category and severity.
     *
     * @return PaginatedResponse<Issue>
     */
    public function list(
        string $scanId,
        ?string $category = null,
        ?string $severity = null,
        int $perPage = 50,
    ): PaginatedResponse {
        $query = ['per_page' => $perPage];

        if ($category !== null) {
            $query['category'] = $category;
        }

        if ($severity !== null) {
            $query['severity'] = $severity;
        }

        $response = $this->client->get("{$this->teamId}/scans/{$scanId}/issues", $query);

        return PaginatedResponse::from($response, fn (array $data) => Issue::from($data));
    }
}
