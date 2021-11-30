<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Commands;

use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;

class LaravelShortenUrlsCommand extends Command
{
    /** @var string $signature */
    protected $signature = 'url:shorten {url : The URL to shorten}';

    /** @var string $description */
    protected $description = 'Shorten a URL using the registered service';

    public function handle(): int
    {
        $service = $this->getLaravel()->make(UrlShorteningService::class);

        if (!$service instanceof UrlShorteningService) {
            throw new BindingResolutionException(
                'Cannot get Url Shortening Service from Container'
            );
        }

        $url = $this->input->getArgument('url');

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
            ],
        );

        return self::SUCCESS;
    }
}
