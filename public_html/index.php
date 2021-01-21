<?php
define('DEBUG', true);

if (DEBUG === true) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

include __DIR__ . '/../app/bootstrap.php';
