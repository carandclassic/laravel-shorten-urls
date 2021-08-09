<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Tests;

use CarAndClassic\LaravelShortenUrls\LaravelShortenUrlsServiceProvider;
use CarAndClassic\LaravelShortenUrls\ServiceProviders\MainServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'CarAndClassic\\LaravelShortenUrls\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MainServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        include_once __DIR__.'/../database/migrations/create_laravel-shorten-urls_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}