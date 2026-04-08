<?php

declare(strict_types=1);

namespace ScanLyser\Data;

/**
 * @template T
 */
readonly class PaginatedResponse
{
    /**
     * @param  array<int, T>  $data
     */
    public function __construct(
        public array $data,
        public int $currentPage,
        public int $perPage,
        public int $total,
        public int $lastPage,
    ) {}

    /**
     * Create from API response.
     *
     * @template TItem
     *
     * @param  array<string, mixed>  $response
     * @param  callable(array<string, mixed>): TItem  $hydrator
     * @return self<TItem>
     */
    public static function from(array $response, callable $hydrator): self
    {
        $pagination = $response['meta']['pagination'] ?? $response['meta'] ?? [];

        return new self(
            data: array_map($hydrator, $response['data'] ?? []),
            currentPage: $pagination['current_page'] ?? 1,
            perPage: $pagination['per_page'] ?? 15,
            total: $pagination['total'] ?? 0,
            lastPage: $pagination['last_page'] ?? 1,
        );
    }
}
