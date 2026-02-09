<?php
declare(strict_types=1);

/**
 * index.php
 *
 * Punt d'entrada principal de l'aplicació MVC.
 */

// -----------------------------------------------------------------------------
// PATHS
// -----------------------------------------------------------------------------
define('APP_ROOT', dirname(__DIR__));
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

// -----------------------------------------------------------------------------
// SESSIÓ
// -----------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------------------
// AUTOLOAD
// -----------------------------------------------------------------------------
require_once APP_ROOT . '/vendor/autoload.php';

// -----------------------------------------------------------------------------
// ENV
// -----------------------------------------------------------------------------
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

// -----------------------------------------------------------------------------
// LOGGER
// -----------------------------------------------------------------------------
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Infrastructure\Routing\Router;

$loggerMng = new Logger('mvc');

$loggerMng->pushHandler(
    new StreamHandler(APP_ROOT . '/src/Infrastructure/Logging/logs/mvcError.log', Logger::ERROR)
);

$loggerMng->pushHandler(
    new StreamHandler(APP_ROOT . '/src/Infrastructure/Logging/logs/mvcWarnings.log', Logger::WARNING)
);

$loggerMng->pushHandler(
    new StreamHandler(APP_ROOT . '/src/Infrastructure/Logging/logs/mvcApp.log', Logger::INFO)
);

// -----------------------------------------------------------------------------
// APP
// -----------------------------------------------------------------------------
try {    
    // INICIALITZACIÓ BD
    $router = new Router($loggerMng);

    /**
     * -------------------------------
     * RUTES GET I POST DE L'APLICACIÓ
     * -------------------------------
     */
    
    // Ruta principal de la pàgina home
    $router->get('/', 'HomeController@index');
    
    // Llistat de totes les tasques
    $router->get('/tasques', 'PostController@index');
    
    // Formulari de creació d'una nova tasca
    $router->get('/tasques/create', 'PostController@create');
    
    // Processar la creació d'una nova tasca
    $router->post('/tasques', 'PostController@store');
    
    // Formulari d'edició d'una tasca concreta
    $router->get('/tasques/{id}/edit', 'PostController@edit');
    
    // Processar actualització d'una tasca concreta
    $router->post('/tasques/{id}', 'PostController@update');
    
    // Eliminar una tasca concreta
    $router->post('/tasques/{id}/delete', 'PostController@delete');

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $router->dispatch($uri);

} catch (RuntimeException $e) {
    $loggerMng->error($e->getMessage(), ['exception' => $e]);
    echo "Ha succeït un error inesperat.";
}
