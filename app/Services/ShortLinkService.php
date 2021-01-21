<?php

namespace App\Services;

use Algo26\IdnaConvert\Exception\AlreadyPunycodeException;
use Algo26\IdnaConvert\ToIdn;
use App\Lib\Helper;

class ShortLinkService
{
    /**
     * @param int $number
     * @return string
     */
    public static function covertNumberToShortCode(int $number): string
    {
        return Helper::convertDecToX62($number);
    }

    /**
     * @param string $code
     * @return int|null
     */
    public static function convertShortCodeToNumber(string $code): ?int
    {
        return Helper::convertX62ToDec($code);
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function isUrlExists(string $url): bool
    {
        $IDN = new ToIdn();

        try {
            $urlPunycode = $IDN->convertUrl($url);
        } catch (AlreadyPunycodeException $e) {
            $urlPunycode = $url;
        }

        return (self::hasResponse($url) ?: self::hasResponse(Helper::urlEncode($url)))
            || (self::hasResponse($urlPunycode) ?: self::hasResponse(Helper::urlEncode($urlPunycode)));
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function isStatus200(string $url): bool
    {
        $opts = [
            'http' => [
                'method' => 'HEAD',
            ],
        ];
        $context = stream_context_create($opts);
        $headers = @get_headers($url, 1, $context);

        if (is_array($headers) && strpos($headers[0], '200')) {
            return true;
        }

        if (
            is_array($headers)
            && (strpos($headers[0], '301') || strpos($headers[0], '302'))
            && !empty($headers['Location'])
        ) {
            return static::isUrlExists($headers['Location']);
        }

        return false;
    }

    /**
     * @param $url
     * @return bool
     */
    public static function hasResponse($url): bool
    {
        $opts = [
            'http' => [
                'method' => 'HEAD',
            ],
        ];
        $context = stream_context_create($opts);
        $headers = @get_headers($url, 1, $context);

        return is_array($headers);
    }
}