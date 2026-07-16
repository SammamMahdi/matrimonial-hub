<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

define('BASE_PATH', dirname(__DIR__));

// ---------------------------------------------------------------- autoloading
// PSR-4 style: App\Core\Database -> app/Core/Database.php. No Composer needed,
// which keeps the project a drop-in for XAMPP's htdocs.
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/helpers.php';

// --------------------------------------------------------------------- config
$configFile = BASE_PATH . '/config/config.php';

if (!is_file($configFile)) {
    http_response_code(500);
    exit(
        "<h1>Setup required</h1><p>Copy <code>config/config.example.php</code> to "
        . "<code>config/config.php</code> and set your database credentials.</p>"
    );
}

/** @var array $config */
$config = require $configFile;

// -------------------------------------------------------------- error display
if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');

    set_exception_handler(static function (Throwable $e): void {
        error_log((string) $e);
        http_response_code(500);
        echo View::render('errors/500', [], 'layouts/blank');
    });
}

// --------------------------------------------------------------------- kernel
$request = Request::capture();

Session::start($config['session']);
Database::boot($config['db']);
View::setPath(BASE_PATH . '/app/Views');
Response::setBasePath($config['app']['url'] !== '' ? rtrim($config['app']['url'], '/') : $request->basePath());

app_config($config);

return $request;
