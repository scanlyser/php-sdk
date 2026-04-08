<?php

declare(strict_types=1);

namespace ScanLyser;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use ScanLyser\Exceptions\AuthenticationException;
use ScanLyser\Exceptions\ForbiddenException;
use ScanLyser\Exceptions\NotFoundException;
use ScanLyser\Exceptions\RateLimitException;
use ScanLyser\Exceptions\ScanLyserException;
use ScanLyser\Exceptions\ValidationException;
use ScanLyser\Resources\IssueResource;
use ScanLyser\Resources\PageResource;
use ScanLyser\Resources\ReportResource;
use ScanLyser\Resources\ScanResource;
use ScanLyser\Resources\SiteResource;
use ScanLyser\Resources\TeamResource;

class Client
{
    private HttpClient $http;

    private int $maxRetries;

    /**
     * Create a new ScanLyser client.
     */
    public function __construct(
        private readonly string $apiKey,
        int $maxRetries = 3,
        ?HttpClient $http = null,
    ) {
        $this->maxRetries = $maxRetries;
        $this->http = $http ?? new HttpClient([
            'base_uri' => 'https://scanlyser.app/api/v1/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Access team resources.
     */
    public function teams(): TeamResource
    {
        return new TeamResource($this);
    }

    /**
     * Access site resources scoped to a team.
     */
    public function sites(string $teamId): SiteResource
    {
        return new SiteResource($this, $teamId);
    }

    /**
     * Access scan resources scoped to a team.
     */
    public function scans(string $teamId): ScanResource
    {
        return new ScanResource($this, $teamId);
    }

    /**
     * Access page resources scoped to a team.
     */
    public function pages(string $teamId): PageResource
    {
        return new PageResource($this, $teamId);
    }

    /**
     * Access issue resources scoped to a team.
     */
    public function issues(string $teamId): IssueResource
    {
        return new IssueResource($this, $teamId);
    }

    /**
     * Access report resources scoped to a team.
     */
    public function reports(string $teamId): ReportResource
    {
        return new ReportResource($this, $teamId);
    }

    /**
     * Send a GET request to the API.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    /**
     * Send a POST request to the API.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    /**
     * Send a DELETE request to the API.
     */
    public function delete(string $path): void
    {
        $this->request('DELETE', $path);
    }

    /**
     * Send a GET request and return the raw response body.
     */
    public function getRaw(string $path, array $query = []): string
    {
        return $this->requestRaw('GET', $path, ['query' => $query]);
    }

    /**
     * Send an HTTP request with automatic retry on rate limiting.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $options = []): array
    {
        $body = $this->requestRaw($method, $path, $options);

        if ($body === '') {
            return [];
        }

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Send an HTTP request and return raw response body.
     *
     * @param  array<string, mixed>  $options
     */
    private function requestRaw(string $method, string $path, array $options = []): string
    {
        $attempts = 0;

        while (true) {
            try {
                $response = $this->http->request($method, $path, $options);

                return (string) $response->getBody();
            } catch (ClientException $exception) {
                $status = $exception->getResponse()->getStatusCode();
                $body = (string) $exception->getResponse()->getBody();
                $decoded = json_decode($body, true) ?? [];

                if ($status === 429 && $attempts < $this->maxRetries) {
                    $retryAfter = (int) ($exception->getResponse()->getHeaderLine('Retry-After') ?: 1);
                    sleep($retryAfter);
                    $attempts++;

                    continue;
                }

                throw $this->mapException($status, $decoded);
            } catch (ServerException $exception) {
                $status = $exception->getResponse()->getStatusCode();
                $body = (string) $exception->getResponse()->getBody();
                $decoded = json_decode($body, true) ?? [];

                throw $this->mapException($status, $decoded);
            }
        }
    }

    /**
     * Map an API error response to a typed exception.
     *
     * @param  array<string, mixed>  $body
     */
    private function mapException(int $status, array $body): ScanLyserException
    {
        $error = $body['error'] ?? [];
        $message = $error['message'] ?? "API request failed with status {$status}";
        $errors = $error['errors'] ?? [];

        return match ($status) {
            401 => new AuthenticationException($message),
            403 => new ForbiddenException($message),
            404 => new NotFoundException($message),
            422 => new ValidationException($message, $errors),
            429 => new RateLimitException($message),
            default => new ScanLyserException($message, $status),
        };
    }
}
