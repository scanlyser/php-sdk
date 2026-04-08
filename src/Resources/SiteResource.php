<?php

declare(strict_types=1);

namespace ScanLyser\Resources;

use ScanLyser\Data\PaginatedResponse;
use ScanLyser\Data\Site;
use ScanLyser\Client;

readonly class SiteResource
{
    public function __construct(
        private Client $client,
        private string $teamId,
    ) {}

    /**
     * List all sites for the team.
     *
     * @return PaginatedResponse<Site>
     */
    public function list(int $perPage = 15): PaginatedResponse
    {
        $response = $this->client->get("{$this->teamId}/sites", ['per_page' => $perPage]);

        return PaginatedResponse::from($response, fn (array $data) => Site::from($data));
    }

    /**
     * Create a new site.
     */
    public function create(string $name, string $url): Site
    {
        $response = $this->client->post("{$this->teamId}/sites", [
            'name' => $name,
            'url' => $url,
        ]);

        return Site::from($response['data']);
    }

    /**
     * Get a single site by ID.
     */
    public function get(string $siteId): Site
    {
        $response = $this->client->get("{$this->teamId}/sites/{$siteId}");

        return Site::from($response['data']);
    }

    /**
     * Delete a site.
     */
    public function delete(string $siteId): void
    {
        $this->client->delete("{$this->teamId}/sites/{$siteId}");
    }
}
