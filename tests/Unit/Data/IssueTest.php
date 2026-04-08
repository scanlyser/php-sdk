<?php

use ScanLyser\Data\Issue;
use ScanLyser\Enums\IssueCategory;
use ScanLyser\Enums\IssueSeverity;

it('hydrates from api response', function () {
    $issue = Issue::from([
        'type' => 'accessibility.missing_alt_text',
        'category' => 'wcag',
        'severity' => 'major',
        'message' => 'Image is missing alternative text',
        'url' => 'https://example.com/about',
        'culprits' => ['<img src="photo.jpg">'],
        'help_url' => 'https://dequeuniversity.com/rules/axe/4.4/image-alt',
    ]);

    expect($issue->type)->toBe('accessibility.missing_alt_text')
        ->and($issue->category)->toBe(IssueCategory::Wcag)
        ->and($issue->severity)->toBe(IssueSeverity::Major)
        ->and($issue->message)->toBe('Image is missing alternative text')
        ->and($issue->culprits)->toHaveCount(1)
        ->and($issue->helpUrl)->toBe('https://dequeuniversity.com/rules/axe/4.4/image-alt');
});

it('handles missing optional fields', function () {
    $issue = Issue::from([
        'type' => 'seo.missing_meta',
        'category' => 'seo',
        'severity' => 'minor',
        'message' => 'Missing meta description',
        'url' => 'https://example.com',
    ]);

    expect($issue->culprits)->toBe([])
        ->and($issue->helpUrl)->toBeNull();
});
