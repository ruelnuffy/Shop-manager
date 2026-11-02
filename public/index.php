<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Normalize SCRIPT_NAME and PHP_SELF for subfolder deployments via root .htaccess
if (isset($_SERVER['REQUEST_URI']) && ! str_contains($_SERVER['REQUEST_URI'], '/public/')) {
    if (isset($_SERVER['SCRIPT_NAME'])) {
        $_SERVER['SCRIPT_NAME'] = str_replace('/public/index.php', '/index.php', $_SERVER['SCRIPT_NAME']);
    }
    if (isset($_SERVER['PHP_SELF'])) {
        $_SERVER['PHP_SELF'] = str_replace('/public/index.php', '/index.php', $_SERVER['PHP_SELF']);
    }

    // If accessing subfolder root without trailing slash (e.g. /test), redirect to /test/
    $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($uriPath && isset($_SERVER['SCRIPT_NAME'])) {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/' && $scriptDir !== '.' && $scriptDir !== '' && $uriPath === $scriptDir) {
            $query = ! empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
            header('Location: '.$scriptDir.'/'.$query, true, 301);
            exit;
        }
    }
}

$app->handleRequest(Request::capture());
