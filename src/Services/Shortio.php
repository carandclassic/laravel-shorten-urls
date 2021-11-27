<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Services;

use CarAndClassic\LaravelShortenUrls\Contracts\UrlShorteningService;
use CarAndClassic\LaravelShortenUrls\Exceptions\ApiConfigurationError;
use CarAndClassic\LaravelShortenUrls\Exceptions\ApiResponseFailure;
use CarAndClassic\LaravelShortenUrls\Values\ShortLink;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Shortio implements UrlShorteningService
{

    public function shortenUrl(string $url, array $additionalParams = []): ?ShortLink
    {
        $domain = (string)config('shorten-urls.provider-list.shortio.domain');
        $apiKey = (string)config('shorten-urls.provider-list.shortio.api_key');

        if (empty($apiKey)) {
            throw new ApiConfigurationError('Short.io Configuration Error: Missing Api Key');
        }

        if (empty($domain)) {
            throw new ApiConfigurationError('Short.io Configuration Error: Missing Domain');
        }

//        try {
        $settings = array_merge(
            $additionalParams,
            [
                'originalURL' => $url,
                'domain' => $domain
            ]
        );

        $client = new Client();
        try {
            $response = $client->request(
                'POST',
                'https://api.short.io/links',
                [
                    'body' => $settings,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => $apiKey,
                        'Content-Type' => 'application/json',
                    ],

                ]
            );
        } catch (GuzzleException $e) {
        }

        if ($response->getStatusCode() !== 200) {
            throw new ApiResponseFailure(
                'Short.io Response error:' . $response->getBody()->getContents(),
                $response->getStatusCode()
            );
        }

        return ShortLink::create('https://www.google.com');
    }
}
