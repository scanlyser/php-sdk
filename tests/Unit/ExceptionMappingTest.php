<?php

use GuzzleHttp\Psr7\Response;
use ScanLyser\Exceptions\AuthenticationException;
use ScanLyser\Exceptions\ForbiddenException;
use ScanLyser\Exceptions\NotFoundException;
use ScanLyser\Exceptions\RateLimitException;
use ScanLyser\Exceptions\ValidationException;

it('throws authentication exception on 401', function () {
    $client = mockClient([
        new Response(401, [], json_encode([
            'error' => ['status' => 401, 'message' => 'Unauthenticated.'],
        ])),
    ]);

    $client->sites('team_01')->list();
})->throws(AuthenticationException::class, 'Unauthenticated.');

it('throws forbidden exception on 403', function () {
    $client = mockClient([
        new Response(403, [], json_encode([
            'error' => ['status' => 403, 'message' => 'API access requires Agency plan.'],
        ])),
    ]);

    $client->sites('team_01')->list();
})->throws(ForbiddenException::class, 'API access requires Agency plan.');

it('throws not found exception on 404', function () {
    $client = mockClient([
        new Response(404, [], json_encode([
            'error' => ['status' => 404, 'message' => 'Site not found.'],
        ])),
    ]);

    $client->sites('team_01')->get('nonexistent');
})->throws(NotFoundException::class, 'Site not found.');

it('throws validation exception on 422 with errors', function () {
    $client = mockClient([
        new Response(422, [], json_encode([
            'error' => [
                'status' => 422,
                'message' => 'The given data was invalid.',
                'errors' => ['url' => ['The url field is required.']],
            ],
        ])),
    ]);

    try {
        $client->sites('team_01')->create(name: 'Test', url: '');
    } catch (ValidationException $exception) {
        expect($exception->getMessage())->toBe('The given data was invalid.')
            ->and($exception->errors)->toHaveKey('url')
            ->and($exception->errors['url'][0])->toBe('The url field is required.');

        return;
    }

    $this->fail('Expected ValidationException was not thrown.');
});

it('retries on 429 and throws after max retries', function () {
    $client = mockClient([
        new Response(429, ['Retry-After' => '0'], json_encode([
            'error' => ['status' => 429, 'message' => 'Too many requests.'],
        ])),
        new Response(429, ['Retry-After' => '0'], json_encode([
            'error' => ['status' => 429, 'message' => 'Too many requests.'],
        ])),
        new Response(429, ['Retry-After' => '0'], json_encode([
            'error' => ['status' => 429, 'message' => 'Too many requests.'],
        ])),
        new Response(429, ['Retry-After' => '0'], json_encode([
            'error' => ['status' => 429, 'message' => 'Too many requests.'],
        ])),
    ]);

    $client->sites('team_01')->list();
})->throws(RateLimitException::class);

it('succeeds after retry on 429', function () {
    $client = mockClient([
        new Response(429, ['Retry-After' => '0'], json_encode([
            'error' => ['status' => 429, 'message' => 'Too many requests.'],
        ])),
        jsonResponse([
            'data' => [],
            'meta' => ['status' => 200, 'current_page' => 1, 'per_page' => 15, 'total' => 0, 'last_page' => 1],
        ]),
    ]);

    $result = $client->sites('team_01')->list();

    expect($result->data)->toBe([]);
});
