<?php

declare(strict_types=1);

namespace ScanLyser\Data;

readonly class Team
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $personalTeam,
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
            personalTeam: $data['personal_team'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
        );
    }
}
