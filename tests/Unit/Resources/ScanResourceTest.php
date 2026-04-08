<?php

use ScanLyser\Data\Scan;
use ScanLyser\Enums\ScanStatus;
use ScanLyser\Enums\WcagLevel;

it('triggers a scan', function () {
    $client = mockClient([
        jsonResponse([
            'data' => [
                'id' => 'scan_new',
                'site_id' => 'site_01',
                'status' => 'pending',
                'wcag_level' => 'AA',
                'pages_crawled' => 0,
                'pages_total' => 0,
                'issues_count' => 0,
                'created_at' => '2026-04-08T00:00:00Z',
            ],
            'meta' => ['status' => 202],
        ], 202),
    ]);

    $scan = $client->scans('team_01')->trigger('site_01', wcagLevel: 'AA');

    expect($scan)->toBeInstanceOf(Scan::class)
        ->and($scan->id)->toBe('scan_new')
        ->and($scan->status)->toBe(ScanStatus::Pending)
        ->and($scan->wcagLevel)->toBe(WcagLevel::AA);
});

it('gets a scan with scores', function () {
    $client = mockClient([
        jsonResponse([
            'data' => [
                'id' => 'scan_01',
                'site_id' => 'site_01',
                'status' => 'completed',
                'wcag_level' => 'AAA',
                'pages_crawled' => 50,
                'pages_total' => 50,
                'issues_count' => 120,
                'scores' => [
                    'overall' => 72, 'wcag' => 65, 'seo' => 80,
                    'performance' => 85, 'ux' => 70, 'sitewide' => 60, 'other' => 75,
                ],
                'created_at' => '2026-01-01T00:00:00Z',
                'completed_at' => '2026-01-01T02:00:00Z',
            ],
            'meta' => ['status' => 200],
        ]),
    ]);

    $scan = $client->scans('team_01')->get('scan_01');

    expect($scan->status)->toBe(ScanStatus::Completed)
        ->and($scan->isTerminal())->toBeTrue()
        ->and($scan->scores)->not->toBeNull()
        ->and($scan->scores->overall)->toBe(72.0)
        ->and($scan->wcagLevel)->toBe(WcagLevel::AAA);
});

it('detects terminal scan states', function () {
    $completed = Scan::from([
        'id' => 's1', 'site_id' => 'x', 'status' => 'completed', 'wcag_level' => 'AA',
        'pages_crawled' => 1, 'pages_total' => 1, 'issues_count' => 0,
        'created_at' => '2026-01-01T00:00:00Z',
    ]);

    $failed = Scan::from([
        'id' => 's2', 'site_id' => 'x', 'status' => 'failed', 'wcag_level' => 'AA',
        'pages_crawled' => 0, 'pages_total' => 0, 'issues_count' => 0,
        'created_at' => '2026-01-01T00:00:00Z', 'failure_reason' => 'Bot protection',
    ]);

    $pending = Scan::from([
        'id' => 's3', 'site_id' => 'x', 'status' => 'pending', 'wcag_level' => 'AA',
        'pages_crawled' => 0, 'pages_total' => 0, 'issues_count' => 0,
        'created_at' => '2026-01-01T00:00:00Z',
    ]);

    expect($completed->isTerminal())->toBeTrue()
        ->and($failed->isTerminal())->toBeTrue()
        ->and($pending->isTerminal())->toBeFalse();
});
