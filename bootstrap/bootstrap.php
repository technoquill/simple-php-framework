<?php
declare(strict_types=1);

define('APP_BASE_PATH', dirname(__DIR__));

error_reporting(E_ALL);

use Dotenv\Dotenv;


// Bootstrap everything (env, autoload, helpers, config, DI, events, etc.)
require APP_BASE_PATH.'/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(APP_BASE_PATH);
$dotenv->safeLoad();
