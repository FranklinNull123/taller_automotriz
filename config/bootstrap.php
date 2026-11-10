<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$envPath = dirname(__DIR__);
if (class_exists(Dotenv::class) && is_file($envPath . '/.env')) {
    Dotenv::createImmutable($envPath)->safeLoad();
}

function envValue($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

function appLogger() {
    static $logger;
    if ($logger instanceof Logger) {
        return $logger;
    }

    $logDirectory = dirname(__DIR__) . '/logs';
    if (!is_dir($logDirectory)) {
        mkdir($logDirectory, 0750, true);
    }
    $logger = new Logger('taller-automotriz');
    $logger->pushHandler(new StreamHandler($logDirectory . '/app.log', Logger::INFO));
    return $logger;
}
