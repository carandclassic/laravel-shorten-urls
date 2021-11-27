<?php

declare(strict_types=1);

use CarAndClassic\LaravelShortenUrls\Services\Bitly;
use CarAndClassic\LaravelShortenUrls\Services\Shortio;

return [

    'provider' => 'bitly',

    'provider-list' => [
        'bitly' => [
            'service_class' => Bitly::class,

            /*
             * API Key - this is set in env
             */
            'api_key' => env('BITLY_API_KEY', ''),

            /*
             * CURL Options - https://github.com/phplicengine/bitly#custom-curl-options
             *
             * these here are in format of CURLOPT_* => value
             */
            'curl_options' => [
            ],
        ],
        'shortio' => [
            'service_class' => Shortio::class,

            'api_key' => env('SHORTIO_API_KEY', ''),

            'domain' => env('SHORTIO_API_DOMAIN', ''),
        ],
    ]
];
