<?php

namespace App\Lib;

/**
 * Class Helper
 * @package App\Core
 */
class Helper
{
    public const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDIFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @param $name
     * @return string
     */
    public static function hyphenToUpperCase($name): string
    {
        $nameArray = explode('-', $name);

        $nameArray = array_map(static function ($item) {
            return ucfirst(strtolower($item));
        }, $nameArray);

        return implode('', $nameArray);
    }

    /**
     * @param $name
     * @return string
     */
    public static function upperCaseToHyphen($name): string
    {
        $name = preg_replace( '/([a-z])([A-Z])/', '$1-$2', $name);
        $name = preg_replace( '/(\d+)/', '-$1-', strtolower($name));
        return trim($name, '-');
    }

    /**
     * @param int $decimal
     * @return string
     */
    public static function convertDecToX62(int $decimal): string
    {
        $charactersLength = strlen(static::CHARACTERS);
        $x62 = '';

        do {
            $modulo = $decimal % $charactersLength;
            $decimal = (int)($decimal / $charactersLength);
            $x62 = static::CHARACTERS[$modulo] . $x62;
        } while ($decimal >= $charactersLength);

        return $decimal !== 0 ? static::CHARACTERS[$decimal] . $x62 : $x62;
    }

    /**
     * @param string $x62
     * @return int|null
     */
    public static function convertX62ToDec(string $x62): ?int
    {
        $length = strlen($x62);
        $last = $length - 1;
        $decimal = 0;

        for ($i = $last; $i >= 0; $i--) {
            $char = $x62[$i];
            $charValue = strpos(static::CHARACTERS, $char);

            if ($charValue === false) {
                return null;
            }

            $decimal += $charValue * (62 ** ($last - $i));
        }

        return $decimal;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function trimUrl(string $string): string
    {
        return trim($string, '/ \n\r\t\v');
    }

    /**
     * @param string $url
     * @return string
     */
    public static function urlEncode(string $url): string
    {
        $parts = parse_url($url);
        $query = !empty($parts['query']) ? '?' . urlencode($parts['query']) : '';
        $path = !empty($parts['path']) ? self::trimUrl($parts['path']) : '';
        $path = $path === '' ? '' : '/' . implode('/', array_map(static function ($item) {
                return rawurlencode($item);
            }, explode('/', $path)));
        $url = sprintf('%s://%s%s%s', $parts['scheme'], $parts['host'], $path, $query);
        return $url;
    }
}