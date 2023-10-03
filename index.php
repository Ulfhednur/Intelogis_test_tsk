<?php
restore_error_handler();
restore_exception_handler();
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Lib\Application;

const APP_PATH = __DIR__;

spl_autoload_register(function ($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    include_once APP_PATH.DIRECTORY_SEPARATOR.$className . '.php';
});

$app = Application::getInstance();
$app->run();