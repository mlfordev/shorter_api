<?php

define('PROJECT_ROOT', dirname(__DIR__));

require PROJECT_ROOT . '/vendor/autoload.php';

$params = \App\Core\Params::getInstance();
$dbConfig = [
    'default' => [
        'host' => $params->getValue('db_host'),
        'dbname' => $params->getValue('db_name'),
        'user' => $params->getValue('db_user'),
        'password' => $params->getValue('db_password'),
        'charset' => $params->getValue('db_charset'),
        'driver' => $params->getValue('db_driver'),
    ]
];

\Phact\Orm\Configuration\ConfigurationProvider::setDbConfig($dbConfig);
$app = \App\Core\Application::getInstance();
$response = $app->run();
if (!$app->isCliMode()) {
    $response->send();
}
