<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Contracts;

use CarAndClassic\LaravelShortenUrls\Values\ShortLink;

interface UrlShorteningService
{
    public function shortenUrl(string $url, array $additionalParams = []): ?ShortLink;

    /**
     * @return array|bool true on success, array of errors on failure
     */
    public function verifyConfig();
}
