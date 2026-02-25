<?php
declare(strict_types=1);


define('APP_ROOT', dirname(__DIR__));
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_ROOT . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Http\Routing\Router;
use App\Http\Routing\Redirect;

$dotenv = Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

$loggerMng = new Logger('mvc');
$loggerMng->pushHandler(new StreamHandler(APP_ROOT . '/src/Infrastructure/Logging/logs/mvcError.log', Logger::ERROR));
$loggerMng->pushHandler(new StreamHandler(APP_ROOT . '/src/Infrastructure/Logging/logs/mvcWarnings.log', Logger::WARNING));
$loggerMng->pushHandler(new StreamHandler(APP_ROOT . '/src/Infrastructure/Logging/logs/mvcApp.log', Logger::INFO));

try {
    $router = new Router($loggerMng);

    // Carregar rutes des de config/routes.php
    $registerRoutes = require APP_ROOT . '/config/routes.php';
    $registerRoutes($router);

    $router->dispatch($_SERVER['REQUEST_URI']);

} catch (RuntimeException $e) {
    $loggerMng->error($e->getMessage(), ['exception' => $e]);

    // Comprovar rol
    $role = $_SESSION['user_role'] ?? 'user'; 

    if ($role === 'admin') {
        Redirect::withError('/admin/audit', 'Ha succeït un error: ' . $e->getMessage());
    } 
    else {
        Redirect::withError('/', 'Ha succeït un error: ' . $e->getMessage());
    }

    exit;
}