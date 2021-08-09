<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Commands;

use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;

class LaravelShortenUrlsCommand extends Command
{
    public $signature = 'url:shorten {url : The URL to shorten}';
    public $description = 'Shorten an URL using the registered service';

    public function handle(): void
    {
        $service = app(UrlShorteningService::class);
        if (! $service instanceof UrlShorteningService) {
            throw new BindingResolutionException('Cannot get Url Shortening Service from Container');
        }

        $url = (string)$this->argument('url');

        $this->getOutput()->table(
            [
                'Long Url',
                'Short Url',
            ],
            [
                [
                    $url,
                    $service->shortenUrl($url),
                ],
            ]
        );
    }
}
