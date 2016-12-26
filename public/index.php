<?php
/** Default timezone (if you need) */
date_default_timezone_set('America/Fortaleza');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/../src/config/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/config/dependencies.php';

// Register middleware
require __DIR__ . '/../src/config/middleware.php';

// Register routes
require __DIR__ . '/../src/config/routes.php';

// Run app
$app->run();
