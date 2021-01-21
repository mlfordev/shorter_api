<?php

namespace App\Core\Routers;

use App\Controllers\NotFoundController;
use App\Core\Controller;
use App\Core\HttpRequest;
use App\Core\HttpResponse;
use App\Core\View;
use App\Models\Admin;

/**
 * Class WebRouter
 * @package App\Components\Routers
 */
class WebRouter extends Router
{
    /** @var array */
    private $routes = [];

    /**
     * @param string $method
     * @param string $route
     * @param array $target
     */
    public function add(string $method, string $route, array $target): void
    {
        $pattern = '/^' . str_replace('/', '\/', $route) . '$/';
        $this->routes[$method][$pattern] = $target;
    }

    /**
     * @return HttpResponse
     */
    public function handleRequest(): HttpResponse
    {
        $request = new HttpRequest();
        $url = rtrim(parse_url($request->getServer()->get('REQUEST_URI'), PHP_URL_PATH), '/');
        $url = $url === '' ? '/' : $url;
        $httpMethod = $request->getServer()->get('REQUEST_METHOD');
        $this->compileRoutes();

        $controller = NotFoundController::class;
        $method = 'index';
        $mustBeAdmin = false;

        foreach ($this->routes[$httpMethod] as $pattern => $target) {
            if (preg_match($pattern, $url, $params)) {
                array_shift($params);
                $controller = $target[0];
                $method = $target[1];
                if (!empty($target[2])) {
                    $mustBeAdmin = true;
                }
                break;
            }
        }

        /** @var Controller $controllerInstance */
        $controllerInstance = new $controller($request, new View());

        if ($mustBeAdmin) {
            $login = $request->getServer()->get('PHP_AUTH_USER');
            $password = $request->getServer()->get('PHP_AUTH_PW');
            if (is_null($login) || is_null($password)) {
                $controllerInstance->jsonUnauthorized();
            }

            $admin = Admin::getByLoginOrCreate($login);

            if ($admin->getIsNew() || !$admin->passwordVerify($password)) {
                return $controllerInstance->jsonUnauthorized();
            }
        }

        if (empty($params)) {
            return $controllerInstance->$method();
        }
        return $controllerInstance->$method(...$params);
    }

    /**
     * @return void
     */
    public function compileRoutes(): void
    {
        if (empty($this->routes)) {
            $rawRoutes = include PROJECT_ROOT . '/app/routes.php';

            foreach ($rawRoutes as $route) {
                $this->add($route['method'], $route['route'], $route['target']);
            }
        }
    }
}