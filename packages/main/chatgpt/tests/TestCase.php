<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Dotenv\Dotenv;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadEnv();
    }

    protected function loadEnv(): void
    {
        $dotenv = Dotenv::createMutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}
