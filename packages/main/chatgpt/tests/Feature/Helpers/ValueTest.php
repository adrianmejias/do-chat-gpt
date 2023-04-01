<?php

test('if value is not empty', function () {
    $value = 'test';

    expect(value($value))->toBe('test');
});

test('if value is empty', function () {
    $value = '';

    expect(value($value))->toBeEmpty();
});

test('if value is null', function () {
    $value = null;

    expect(value($value))->toBeNull();
});

test('if value is closure', function () {
    $value = function () {
        return 'test';
    };

    expect(value($value))->toBe('test');
});
