<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Services;

use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use CarAndClassic\LaravelShortenUrls\Exceptions\ApiResponseFailure;
use CarAndClassic\LaravelShortenUrls\Values\ShortLink;
use PHPLicengine\Api\Result;
use PHPLicengine\Service\Bitlink;

class Bitly implements UrlShorteningService
{
    private Bitlink $bitlink;

    public function __construct(Bitlink $bitlink)
    {
        $this->bitlink = $bitlink;
    }

    public function shortenUrl(string $url, array $additionalParams = []): ?ShortLink
    {
        $bitlinkRequestConfig = [
            'long_url' => $url,
        ];

        if (! empty($additionalParams['domain'])) {
            $bitlinkRequestConfig['domain'] = $additionalParams['domain'];
        }

        /** @var Result $response */
        $response = $this->bitlink->createBitlink($bitlinkRequestConfig);
        if ($response->isError()) {
            throw new ApiResponseFailure(
                'Bitly service had an Error'
                . $response->getReasonPhrase()
                . var_export($response->getResponseArray(), true),
                $response->getResponseCode()
            );
        }

        return ShortLink::create($response->getResponseObject()->link);
    }
}
