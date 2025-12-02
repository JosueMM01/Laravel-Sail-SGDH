<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'array',
            'cache.default' => 'array',
            'app.url' => 'http://localhost',
        ]);

        URL::forceScheme('http');
        URL::forceRootUrl(config('app.url'));
    }
}
