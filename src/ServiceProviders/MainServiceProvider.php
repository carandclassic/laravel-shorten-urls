<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\ServiceProviders;

use CarAndClassic\LaravelShortenUrls\Commands\LaravelShortenUrlsCommand;
use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use CarAndClassic\LaravelShortenUrls\Macros\UrlGeneratorShortenMacro;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class MainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            UrlShorteningService::class,
            static function (Container $app) {
                $provider = config('shorten-urls.provider');
                $providerList = collect(config('shorten-urls.provider-list'))
                    ->filter(
                        static fn(array $thisProvider) => array_key_exists('service_class', $thisProvider)
                            && class_exists($thisProvider['service_class'])
                    )
                    ->map(
                        fn($provider) => $provider['service_class']
                    );

                if (!$providerList->has($provider)) {
                    throw new BindingResolutionException(
                        'Cannot load API provider for shortening service: ' . $provider
                    );
                }

                return $app->make($providerList->get($provider));
            }
        );
    }


    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../../config/shorten-urls.php' => config_path('shorten-urls.php'),
            ],
            'laravel-shorten-urls-config'
        );

        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    LaravelShortenUrlsCommand::class,
                ]
            );
        }

//        UrlGenerator::mixin(new UrlGeneratorShortenMacro());
    }
}
