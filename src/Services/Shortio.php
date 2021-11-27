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

        try {
            $settings = json_encode(
                array_merge(
                    $additionalParams,
                    [
                        'originalURL' => $url,
                        'domain' => $domain
                    ]
                ),
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new ApiConfigurationError(
                'Short.io Configuration Error - could not configure settings ' . $e->getMessage()
            );
        }


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
                    'http_errors' => false,
                ]
            );
        } catch (GuzzleException $e) {
            throw new ApiResponseFailure('Short.io error', $e->getCode(), $e);
        }

        $json = json_decode($response->getBody()->getContents(), true);
        switch ($response->getStatusCode()) {
            case 200:
                return ShortLink::create($json['shortURL']);
                break;
            case 400:
                throw new ApiResponseFailure(
                    'Short.io Response error: ' . $json['error'] ?? ' Unknown Error',
                    $response->getStatusCode()
                );
                break;
            case 401:
                throw new ApiResponseFailure(
                    'Short.io Response error: API Key is invalid or not authorised',
                    $response->getStatusCode()
                );
                break;
            case 404:
                throw new ApiResponseFailure(
                    'Short.io Response error: Domain passed not found',
                    $response->getStatusCode()
                );
                break;
            default:
                throw new ApiResponseFailure(
                    'Short.io Response error: ' . $response->getStatusCode() . ' - ' . $response->getBody(
                    )->getContents(),
                    $response->getStatusCode()
                );
        }
    }
}
