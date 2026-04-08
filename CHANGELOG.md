# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-04-08

### Added
- `ScanLyser\Client` — main API client with Bearer token authentication and configurable base URL
- Resource classes for all API endpoints: `TeamResource`, `SiteResource`, `ScanResource`, `PageResource`, `IssueResource`, and `ReportResource`
- Typed data objects with readonly properties: `Team`, `Site`, `Scan`, `ScanPage`, `ScanScores`, `Issue`, and `PaginatedResponse`
- Enum support for `ScanStatus`, `IssueCategory`, `IssueSeverity`, and `WcagLevel`
- Typed exceptions for every API error code: `AuthenticationException` (401), `ForbiddenException` (403), `NotFoundException` (404), `ValidationException` (422), and `RateLimitException` (429), all extending `ScanLyserException`
- Automatic retry on HTTP 429 responses with `Retry-After` header support
- `WebhookSignature::verify()` helper for validating webhook payload signatures
- `ScanResource::awaitCompletion()` polling helper with configurable timeout and interval for waiting on scan results
- Laravel service provider (`ScanLyserServiceProvider`) with auto-discovery support via `extra.laravel`
- Publishable Laravel config file (`config/scanlyser.php`) for API key and base URL configuration
