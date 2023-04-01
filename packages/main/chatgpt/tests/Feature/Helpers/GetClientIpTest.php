<?php

test('if client ip is not empty', function () {
    $_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';

    expect(getClientIp())->toBe('127.0.0.1');
});

test('if client ip is empty', function () {
    $_SERVER['HTTP_CLIENT_IP'] = '';

    expect(getClientIp())->toBeEmpty();
});
