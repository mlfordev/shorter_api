<?php

namespace App\Lib;


use Algo26\IdnaConvert\Exception\AlreadyPunycodeException;
use Algo26\IdnaConvert\Exception\InvalidCharacterException;
use InvalidArgumentException;

class ToIdn extends \Algo26\IdnaConvert\ToIdn
{
    /**
     * @param string $url
     * @return string
     * @throws AlreadyPunycodeException
     * @throws InvalidCharacterException
     */
    public function convertUrl(string $url): string
    {
        $parsed = parse_url($url);
        $_url = str_replace($parsed['scheme'] . '://', '', $url);
        $parsed['host'] = explode('/',$_url)[0];

        if ($parsed === false) {
            throw new InvalidArgumentException('The given string does not look like a URL', 206);
        }

        // Nothing to do
        if (!isset($parsed['host']) || $parsed['host'] === null) {
            return $url;
        }
        $parsed['host'] = $this->convert($parsed['host']);

        return http_build_url($parsed);
    }
}