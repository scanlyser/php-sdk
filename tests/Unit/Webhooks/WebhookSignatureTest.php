<?php

use ScanLyser\Webhooks\WebhookSignature;

it('verifies a valid signature', function () {
    $payload = '{"event":"scan.completed","scan":{"id":"scan_01"}}';
    $secret = 'my-secret-key';
    $signature = 'sha256='.hash_hmac('sha256', $payload, $secret);

    expect(WebhookSignature::verify($payload, $signature, $secret))->toBeTrue();
});

it('rejects an invalid signature', function () {
    $payload = '{"event":"scan.completed","scan":{"id":"scan_01"}}';

    expect(WebhookSignature::verify($payload, 'sha256=invalid', 'my-secret-key'))->toBeFalse();
});

it('rejects a tampered payload', function () {
    $secret = 'my-secret-key';
    $original = '{"event":"scan.completed"}';
    $tampered = '{"event":"scan.failed"}';
    $signature = 'sha256='.hash_hmac('sha256', $original, $secret);

    expect(WebhookSignature::verify($tampered, $signature, $secret))->toBeFalse();
});
