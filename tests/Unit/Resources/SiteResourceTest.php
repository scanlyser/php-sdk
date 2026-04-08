<?php

use ScanLyser\Data\PaginatedResponse;
use ScanLyser\Data\Site;

it('lists sites', function () {
    $client = mockClient([
        jsonResponse([
            'data' => [
                [
                    'id' => 'site_01',
                    'name' => 'Example',
                    'url' => 'https://example.com',
                    'scans_count' => 3,
                    'created_at' => '2026-01-01T00:00:00Z',
                    'updated_at' => '2026-01-02T00:00:00Z',
                ],
            ],
            'meta' => ['status' => 200, 'current_page' => 1, 'per_page' => 15, 'total' => 1, 'last_page' => 1],
        ]),
    ]);

    $result = $client->sites('team_01')->list();

    expect($result)->toBeInstanceOf(PaginatedResponse::class)
        ->and($result->data)->toHaveCount(1)
        ->and($result->data[0])->toBeInstanceOf(Site::class)
        ->and($result->data[0]->name)->toBe('Example')
        ->and($result->data[0]->url)->toBe('https://example.com')
        ->and($result->data[0]->scansCount)->toBe(3);
});

it('creates a site', function () {
    $client = mockClient([
        jsonResponse([
            'data' => [
                'id' => 'site_new',
                'name' => 'New Site',
                'url' => 'https://new.com',
                'created_at' => '2026-04-08T00:00:00Z',
                'updated_at' => '2026-04-08T00:00:00Z',
            ],
            'meta' => ['status' => 201],
        ], 201),
    ]);

    $site = $client->sites('team_01')->create(name: 'New Site', url: 'https://new.com');

    expect($site)->toBeInstanceOf(Site::class)
        ->and($site->id)->toBe('site_new')
        ->and($site->name)->toBe('New Site');
});

it('gets a single site', function () {
    $client = mockClient([
        jsonResponse([
            'data' => [
                'id' => 'site_01',
                'name' => 'Example',
                'url' => 'https://example.com',
                'latest_scan' => [
                    'id' => 'scan_01',
                    'site_id' => 'site_01',
                    'status' => 'completed',
                    'wcag_level' => 'AA',
                    'pages_crawled' => 10,
                    'pages_total' => 10,
                    'issues_count' => 5,
                    'scores' => [
                        'overall' => 85, 'wcag' => 80, 'seo' => 90,
                        'performance' => 85, 'ux' => 88, 'sitewide' => 75, 'other' => 80,
                    ],
                    'created_at' => '2026-01-01T00:00:00Z',
                    'completed_at' => '2026-01-01T01:00:00Z',
                ],
                'scans_count' => 5,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-02T00:00:00Z',
            ],
            'meta' => ['status' => 200],
        ]),
    ]);

    $site = $client->sites('team_01')->get('site_01');

    expect($site->latestScan)->not->toBeNull()
        ->and($site->latestScan->id)->toBe('scan_01')
        ->and($site->latestScan->scores->overall)->toBe(85.0);
});
