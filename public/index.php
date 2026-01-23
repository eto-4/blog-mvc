<?php
/**
 * index.php
 * 
 * Punt d'entrada principal de l'aplicació MVC.
 * Gestiona totes les rutes a través del Router, crida als controladors (HomeController, TaskController, Logger) corresponents i fa la conexió a la bbdd.

 */

// Imports
require_once 'Router.php';
require_once __DIR__ . '/vendor/autoload.php';

// ENV CONFIG
// ==========
use Dotenv\Dotenv;

// Carregar variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ---

// LOGGER CONF
// ==========
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Logger app's
$loggerMng = new Logger('mvc');

// Handler per a logs d'errors.
$loggerMng->pushHandler(
    new StreamHandler(__DIR__ . '/logs/mvcError.log', Logger::ERROR)
);

// Handler per a logs d'avisos.
$loggerMng->pushHandler(
    new StreamHandler(__DIR__ . '/logs/mvcWarnings.log', Logger::WARNING)
);

// Handler per a logs normals.
$loggerMng->pushHandler(
    new StreamHandler(__DIR__ . '/logs/mvcApp.log', Logger::INFO)
);

// ---
// Encapsulació d'errors al log.
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
    $router->get('/tasques', 'TaskController@index');
    
    // Formulari de creació d'una nova tasca
    $router->get('/tasques/create', 'TaskController@create');
    
    // Processar la creació d'una nova tasca
    $router->post('/tasques', 'TaskController@store');
    
    // Formulari d'edició d'una tasca concreta
    $router->get('/tasques/{id}/edit', 'TaskController@edit');
    
    // Processar actualització d'una tasca concreta
    $router->post('/tasques/{id}', 'TaskController@update');
    
    // Eliminar una tasca concreta
    $router->post('/tasques/{id}/delete', 'TaskController@delete');
    
    /**
     * Despatxar la ruta actual
     * Passa la URI del navegador al Router per gestionar la ruta corresponent
     */
    define('APP_ROOT', __DIR__);
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $router->dispatch($uri);

}
catch (RuntimeException $e)
{
    $loggerMng->error($e->getMessage(), ['exception' => $e]);

    // Mostrar missatge amigable a l'usuari
    echo "Ha succeït un error inesperat. Si us plau intenteu-ho de nou més tard.";
}