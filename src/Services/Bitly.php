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

        if ($response->isClientError() && $response->getResponseCode() === 403) {
            throw new ApiResponseFailure(
                'Bitly Service rejected the request: Forbidden. Please check api key in config',
                403
            );
        }

        if (!$response->isOK()) {
            throw new ApiResponseFailure(
                'Bitly service responded in error'
                . $response->getReasonPhrase()
                . var_export($response->getResponseArray(), true),
                $response->getResponseCode()
            );
        }

        if ($response->isError()) {
            throw new ApiResponseFailure(
                'Bitly service had an Error'
                . $response->getReasonPhrase()
                . var_export($response->getResponseObject()->errors, true),
                $response->getResponseCode()
            );
        }

        return ShortLink::create($response->getResponseObject()->link);
    }

    /**
     * @inheritDoc
     */
    public function verifyConfig()
    {
        $apiKey = config('shorten-urls.provider-list.bitly.api_key');
        if (empty($apiKey)) {
            return ['api_key' => 'Bitly Api Key is missing or empty'];
        }

        return true;
    }
}
