# ScanLyser PHP SDK

Official PHP SDK for the [ScanLyser](https://scanlyser.app) API. Run accessibility, SEO, and security scans programmatically.

## Requirements

- PHP 8.2+
- Guzzle 7.0+

## Installation

```bash
composer require scanlyser/php-sdk
```

## Quick Start

```php
use ScanLyser\Client;

$client = new Client(apiKey: 'your-api-token');

// List your sites
$sites = $client->sites($teamId)->list();

foreach ($sites->data as $site) {
    echo "{$site->name}: {$site->url}\n";
}

// Trigger a scan
$scan = $client->scans($teamId)->trigger($siteId, wcagLevel: 'AA');

// Wait for completion
$scan = $client->scans($teamId)->awaitCompletion($scan->id);

// Get issues
$issues = $client->issues($teamId)->list($scan->id, severity: 'critical');
```

## API Reference

### Client

```php
$client = new Client(
    apiKey: 'your-api-token',
    maxRetries: 3, // optional, retries on 429
);
```

### Teams

```php
$teams = $client->teams()->list();
$team = $client->teams()->get($teamId);
```

### Sites

```php
$sites = $client->sites($teamId)->list(perPage: 15);
$site = $client->sites($teamId)->create(name: 'My Site', url: 'https://example.com');
$site = $client->sites($teamId)->get($siteId);
$client->sites($teamId)->delete($siteId);
```

### Scans

```php
$scans = $client->scans($teamId)->list($siteId);
$scan = $client->scans($teamId)->trigger($siteId, wcagLevel: 'AA');
$scan = $client->scans($teamId)->get($scanId);

// Poll until complete (default: 600s timeout, 10s interval)
$scan = $client->scans($teamId)->awaitCompletion(
    $scanId,
    timeoutSeconds: 600,
    pollIntervalSeconds: 10,
);
```

### Pages

```php
$pages = $client->pages($teamId)->list($scanId);
$page = $client->pages($teamId)->get($scanId, $pageId);
```

### Issues

```php
$issues = $client->issues($teamId)->list($scanId);
$issues = $client->issues($teamId)->list($scanId, category: 'wcag', severity: 'critical');
```

### Reports

```php
$report = $client->reports($teamId)->json($scanId);
$client->reports($teamId)->pdf($scanId, saveTo: '/path/to/report.pdf');
```

## Webhook Verification

Verify webhook signatures from scan completion callbacks:

```php
use ScanLyser\Webhooks\WebhookSignature;

$isValid = WebhookSignature::verify(
    payload: $request->getContent(),
    signature: $request->header('X-Signature'),
    secret: $tokenHash,
);
```

## Error Handling

The SDK throws typed exceptions for API errors:

```php
use ScanLyser\Exceptions\AuthenticationException;
use ScanLyser\Exceptions\ForbiddenException;
use ScanLyser\Exceptions\NotFoundException;
use ScanLyser\Exceptions\RateLimitException;
use ScanLyser\Exceptions\ValidationException;

try {
    $site = $client->sites($teamId)->get('nonexistent');
} catch (NotFoundException $exception) {
    // 404
} catch (ValidationException $exception) {
    // 422 - $exception->errors contains field-level errors
} catch (RateLimitException $exception) {
    // 429 - automatic retries exhausted
}
```

Rate-limited requests (429) are automatically retried up to 3 times with the `Retry-After` delay.

## Laravel Integration

The SDK includes an optional service provider with auto-discovery.

### Publish the config:

```bash
php artisan vendor:publish --tag=scanlyser-config
```

### Configure `.env`:

```env
SCANLYSER_TOKEN=your-api-token
SCANLYSER_TEAM=your-team-id
```

### Usage:

```php
use ScanLyser\Client;

class ScanController extends Controller
{
    public function trigger(Client $client): void
    {
        $scan = $client->scans(config('scanlyser.team_id'))
            ->trigger($siteId, wcagLevel: 'AA');
    }
}
```

## License

MIT
