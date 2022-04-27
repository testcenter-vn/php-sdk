<?php

namespace Testcenter\Testcenter\Tests;

use Faker\Factory;
use Illuminate\Support\Str;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineEnvironment($app)
    {
        $faker = Factory::create();
        $app['config']->set('testcenter.client_id', $faker->randomDigit);
        $app['config']->set('testcenter.client_url', $faker->url);
        $app['config']->set('testcenter.partner_secret_key', Str::random(16));
    }
}
