<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\ServiceProviders;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use PHPLicengine\Api\Api;
use PHPLicengine\Api\ApiInterface;

class BitlyApiProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(ApiInterface::class, Api::class);
        $this->app->bind(
            Api::class,
            function () {
                $curlOptions = (array)config()->get('shorten-urls.providers.bitly.curl_options', []);
                $bitlyApi = new Api(config('shorten-urls.providers.bitly.api_key'));
                if (!empty($curlOptions)) {
                    $bitlyApi->setCurlCallback(
                    /**
                     * @param resource $ch
                     */
                        static function ($ch) use ($curlOptions) {
                            foreach ($curlOptions as $curlOption => $optionValue) {
                                curl_setopt($ch, $curlOption, $optionValue);
                            }
                        }
                    );
                }

                return $bitlyApi;
            }
        );
    }

    public function provides(): array
    {
        return [
            ApiInterface::class,
            Api::class,
        ];
    }
}
