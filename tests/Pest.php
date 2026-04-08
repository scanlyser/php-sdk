<?php

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ScanLyser\Client;

/**
 * Create a ScanLyser client with mocked HTTP responses.
 *
 * @param  array<int, Response>  $responses
 */
function mockClient(array $responses): Client
{
    $mock = new MockHandler($responses);
    $handler = HandlerStack::create($mock);
    $http = new HttpClient(['handler' => $handler]);

    return new Client(
        apiKey: 'test-token',
        http: $http,
    );
}

/**
 * Create a JSON response.
 *
 * @param  array<string, mixed>  $body
 */
function jsonResponse(array $body, int $status = 200): Response
{
    return new Response($status, ['Content-Type' => 'application/json'], json_encode($body));
}
