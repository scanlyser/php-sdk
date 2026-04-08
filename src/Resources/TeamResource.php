<?php

declare(strict_types=1);

namespace ScanLyser\Resources;

use ScanLyser\Data\PaginatedResponse;
use ScanLyser\Data\Team;
use ScanLyser\Client;

readonly class TeamResource
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * List all teams accessible to the authenticated token.
     *
     * @return PaginatedResponse<Team>
     */
    public function list(int $perPage = 15): PaginatedResponse
    {
        $response = $this->client->get('teams', ['per_page' => $perPage]);

        return PaginatedResponse::from($response, fn (array $data) => Team::from($data));
    }

    /**
     * Get a single team by ID.
     */
    public function get(string $teamId): Team
    {
        $response = $this->client->get("teams/{$teamId}");

        return Team::from($response['data']);
    }
}
