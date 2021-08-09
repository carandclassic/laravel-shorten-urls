<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Macros;

use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use CarAndClassic\LaravelShortenUrls\Values\ShortLink;
use Illuminate\Routing\UrlGenerator;

/**
 * @mixin UrlGenerator
 * @psalm-suppress UndefinedMethod
 * @psalm-suppress UndefinedThisPropertyFetch
 * @psalm-suppress UndefinedThisPropertyAssignment
 */
class UrlGeneratorShortenMacro
{
    public function shorten(array $additionalParams = []): ?ShortLink
    {
        /** @var UrlGenerator $this */

        return app(UrlShorteningService::class)->shortenUrl(
            $this->current(),
            $additionalParams,
        );
    }
}
