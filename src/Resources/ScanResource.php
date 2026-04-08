<?php

declare(strict_types=1);

namespace ScanLyser\Resources;

use ScanLyser\Data\PaginatedResponse;
use ScanLyser\Data\Scan;
use ScanLyser\Exceptions\ScanLyserException;
use ScanLyser\Client;

readonly class ScanResource
{
    public function __construct(
        private Client $client,
        private string $teamId,
    ) {}

    /**
     * List scans for a site.
     *
     * @return PaginatedResponse<Scan>
     */
    public function list(string $siteId, int $perPage = 15): PaginatedResponse
    {
        $response = $this->client->get("{$this->teamId}/sites/{$siteId}/scans", ['per_page' => $perPage]);

        return PaginatedResponse::from($response, fn (array $data) => Scan::from($data));
    }

    /**
     * Trigger a new scan for a site.
     */
    public function trigger(string $siteId, string $wcagLevel = 'AA', ?string $webhookUrl = null): Scan
    {
        $data = ['wcag_level' => $wcagLevel];

        if ($webhookUrl !== null) {
            $data['webhook_url'] = $webhookUrl;
        }

        $response = $this->client->post("{$this->teamId}/sites/{$siteId}/scans", $data);

        return Scan::from($response['data']);
    }

    /**
     * Get a single scan by ID.
     */
    public function get(string $scanId): Scan
    {
        $response = $this->client->get("{$this->teamId}/scans/{$scanId}");

        return Scan::from($response['data']);
    }

    /**
     * Poll a scan until it reaches a terminal state (completed or failed).
     *
     * @throws ScanLyserException When the timeout is exceeded.
     */
    public function awaitCompletion(string $scanId, int $timeoutSeconds = 600, int $pollIntervalSeconds = 10): Scan
    {
        $start = time();

        while (true) {
            $scan = $this->get($scanId);

            if ($scan->isTerminal()) {
                return $scan;
            }

            if ((time() - $start) >= $timeoutSeconds) {
                throw new ScanLyserException(
                    "Scan {$scanId} did not complete within {$timeoutSeconds} seconds. Last status: {$scan->status->value}",
                );
            }

            sleep($pollIntervalSeconds);
        }
    }
}
