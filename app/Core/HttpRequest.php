<?php

namespace App\Core;

use Phact\Exceptions\InvalidConfigException;
use Phact\Helpers\Collection;

/**
 * Class HttpRequest
 * @package App\Core
 */
class HttpRequest
{
    /**
     * @var string
     */
    public $methodParam = '_method';

    /**
     * @var Collection
     */
    protected $get;

    /**
     * @var Collection
     */
    protected $post;

    /**
     * @var Collection
     */
    protected $patch;

    /**
     * @var Collection
     */
    protected $server;

    private $hostInfo;

    private $securePort;

    private $port;

    private $url;

    /**
     * HttpRequest constructor.
     */
    public function __construct()
    {
        $this->get = new Collection($_GET);
        $this->post = new Collection($_POST);
        $this->server = new Collection($_SERVER);

        if ($this->getMethod() === 'PATCH') {
            $this->patch = new Collection($this->parsePatch());
        }
    }

    /**
     * @return Collection
     */
    public function getGet(): Collection
    {
        return $this->get;
    }

    /**
     * @return Collection
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    /**
     * @return Collection
     */
    public function getPatch(): Collection
    {
        return $this->patch;
    }

    /**
     * @return Collection
     */
    public function getServer(): Collection
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        if (isset($_POST[$this->methodParam])) {
            return strtoupper($_POST[$this->methodParam]);
        }

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * @return string
     */
    public function getHostInfo(): string
    {
        if ($this->hostInfo === null) {
            $secure = $this->getIsSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    $this->hostInfo .= ':' . $port;
                }
            }
        }
        return $this->hostInfo;
    }

    /**
     * @return bool
     */
    public function getIsSecureConnection(): bool
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
            or isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }

    /**
     * @return int
     */
    public function getSecurePort(): int
    {
        if ($this->securePort === null) {
            $this->securePort = $this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
        }
        return $this->securePort;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        if ($this->port === null) {
            $this->port = !$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
        }
        return $this->port;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getUrl(): string
    {
        if ($this->url === null) {
            $this->url = $this->resolveRequestUri();
        }
        return $this->url;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function getPath(): string
    {
        return strtok($this->getUrl(), '?');
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->getServer()->get('QUERY_STRING') ?: '';
    }

    /**
     * @return array
     */
    public function getQueryArray(): array
    {
        $string = $this->getQueryString();
        parse_str($string, $data);
        return $data;
    }

    /**
     * @return array
     */
    protected function parsePatch(): array
    {
        $_PATCH = [];
        $data = '';

        if ($this->getMethod() === 'PATCH' && $patch = fopen('php://input', 'rb')) {
            while (!feof($patch)) {
                $data = fgets($patch, 1024);
            }
            fclose($patch);
            parse_str($data, $_PATCH);
        }

        return $_PATCH;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    protected function resolveRequestUri(): string
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            throw new InvalidConfigException('Unable to determine the request URI.');
        }
        return $requestUri;
    }
}