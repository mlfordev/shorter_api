<?php

namespace App\Core;

use Phact\Helpers\Http;

/**
 * Class Controller
 *
 * @package App\Core
 */
abstract class Controller
{
    /**
     * @var HttpRequest
     */
    public $request;

    /**
     * @var View
     */
    public $view;

    /**
     * Controller constructor.
     * @param HttpRequest $request
     * @param View $view
     */
    public function __construct(HttpRequest $request, View $view)
    {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * @return HttpResponse
     */
    protected function render(): HttpResponse
    {
        $args = func_get_args();
        $content = $this->view->render(...$args);
        return new HttpResponse($content);
    }

    /**
     * @param array|null $data
     * @param int $code
     * @return HttpResponse
     */
    protected function json(?array $data, $code = 200): HttpResponse
    {
        $content = $this->view->jsonEncode($data);
        $headers = 'Content-Type: application/json; charset=utf-8';
        return new HttpResponse($content, $headers, $code);
    }

    /**
     * @param $url
     * @param int $code
     * @return HttpResponse
     */
    protected function redirect($url, $code = 301): HttpResponse
    {
        $headers = 'Location: ' . $url;
        return new HttpResponse('', $headers, $code);
    }

    /**
     * @return HttpResponse
     */
    protected function notFound(): HttpResponse
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        return $this->render('not-found/index', ['title' => '404']);
    }

    /**
     * @return HttpResponse
     */
    public function jsonUnauthorized(): HttpResponse
    {
        header('WWW-Authenticate: Basic rеаlm="Аутентификация"');
        return $this->json(['errors' => ['Неправильные логин или пароль']], 401);
    }
}