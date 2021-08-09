<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Tests;

use CarAndClassic\LaravelShortenUrls\ServiceProviders\MainServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            static fn(string $modelName) => 'CarAndClassic\\LaravelShortenUrls\\Database\\Factories\\'
                . class_basename($modelName)
                . 'Factory'
        );
    }

    /**
     * @param Application $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            MainServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     */
    public function getEnvironmentSetUp($app): void
    {
    }
}
