<?php

declare(strict_types=1);

namespace CarAndClassic\LaravelShortenUrls\Values;

class ShortLink
{
    private string $link = '';

    public static function create(string $url): self
    {
        $shortLink = new self();
        $shortLink->link = $url;

        return $shortLink;
    }

    public function __toString()
    {
        return $this->getLink();
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
