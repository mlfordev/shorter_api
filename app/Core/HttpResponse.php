<?php

namespace App\Core;


use Phact\Helpers\Http;

class HttpResponse
{
    /**
     * @var string|null
     */
    protected $content;

    /**
     * @var string
     */
    protected $headers;

    /**
     * @var int
     */
    protected $code;

    /**
     * HttpResponse constructor.
     * @param string $content
     * @param string $headers
     * @param int $code
     */
    public function __construct(string $content = '', string $headers = 'Content-type: text/html; charset=UTF-8', int $code = 200)
    {
        $this->content = $content;
        $this->headers = $headers;
        $this->code = $code;
    }

    /**
     * @return void
     */
    public function sendHeaders(): void
    {
        if (!headers_sent()) {
            header(sprintf('HTTP/1.1 %s %s', $this->code, Http::getMessage($this->code)));
            header(sprintf('Status: %s %s', $this->code, Http::getMessage($this->code)));
            header($this->headers);
        }
    }

    /**
     * @return void
     */
    public function sendContent(): void
    {
        echo $this->content;
    }

    /**
     * @return void
     */
    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
    }

}