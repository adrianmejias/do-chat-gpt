<?php

test('if variable can be set and returned', function () {
    putenv('FOO=BAR');

    expect(env('FOO'))->toBe('BAR');
});

test('if variable can be returned with default value if not found', function () {
    expect(env('FOOBAR', 'DEFAULT_VALUE'))->toBe('DEFAULT_VALUE');
});

test('if variable can be returned with default value if it is empty', function () {
    putenv('FOOEMPTY=');

    expect(env('FOOEMPTY', 'EMPTY_VALUE'))->toBe('EMPTY_VALUE');
});
