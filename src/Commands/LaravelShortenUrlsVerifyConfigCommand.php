<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Commands;

use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use CarAndClassic\LaravelShortenUrls\Services\Bitly;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;

class LaravelShortenUrlsVerifyConfigCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'url:verify-shorten-config {provider=default}';

    /** @var string $description */
    protected $description = 'Verify the configuration for a URL Shortening service';

    public function handle(): int
    {
        $this->line('Laravel Shorten Urls - Verify Configuration');
        $this->newLine();

        if (!config()->has('shorten-urls')) {
            $this->error('Shorten Urls configuration is missing or not compiled');
            return self::FAILURE;
        }

        if (
            // since by default, missing keys return null, we can skip the has check
            !is_string(config('shorten-urls.provider'))
            || empty(config('shorten-urls.provider'))
        ) {
            $this->error('Shorten Urls configuration provider key (used for default) is missing or empty');
            return self::FAILURE;
        }

        $providerList = config('shorten-urls.provider-list');
        if (
            // since by default, missing keys return null, we can skip the has check
            !is_array($providerList)
            || empty($providerList)
        ) {
            $this->error('Shorten Urls configuration provider configuration list is missing or empty');
            $this->newLine();
            return self::FAILURE;
        }

        $providerName = $this->argument('provider');
        if (strtolower($providerName) === 'default') {
            $providerName = config('shorten-urls.provider');
        }

        if (!array_key_exists($providerName, $providerList)) {
            $this->error('Shorten Urls configuration is missing configuration for ' . $providerName);
            $this->newLine();
            return self::FAILURE;
        }

        $providerConfig = $providerList[$providerName];
        $serviceClass = $providerConfig['service_class']
            ?: ('CarAndClassic\\LaravelShortenUrls\\Services\\' . Str::studly($providerName));

        try {
            $service = $this->getLaravel()->make($serviceClass);
        } catch (BindingResolutionException $e) {
        }

        if (!$service instanceof UrlShorteningService) {
            $this->error(
                'Shorten Urls configuration Service Class for ' . $providerName . ' cannot be created from Container'
            );
            $this->newLine();
            return self::FAILURE;
        }

        $result = $service->verifyConfig();
        if ($result === true) {
            $this->info(
                'Shorten Urls configuration for ' . $providerName . ' passed verification needed to create basic short urls'
            );
            $this->newLine();
            return self::SUCCESS;
        }

        return self::SUCCESS;
    }
}
