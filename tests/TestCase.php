<?php

namespace Tests;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;
use Mockery;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

//    /**
//     * The base URL to use while testing the application.
//     *
//     * @var string
//     */
//    protected $baseUrl = 'http://localhost';

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('data', function($key) {
            return $this->original->getData()[$key];
        });


        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), "Failed asserting that the collection contains the specified value.");
        });


        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), "Failed asserting that the collection does not contain the specified value.");
        });

    }
}


