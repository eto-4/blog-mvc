<?php

declare(strict_types=1);

namespace App\Infrastructure\Routing;

use Psr\Log\LoggerInterface;

/**
 * Router.php
 *
 * Classe Router per gestionar totes les rutes de l'aplicació MVC.
 * Permet definir rutes GET i POST amb controladors o funcions anònimes
 * i despatxar-les segons la URL sol·licitada pel navegador.
 */
class Router
{
    /**
     * @var array $routes
     * Array que emmagatzema totes les rutes registrades
     */
    private array $routes = [];

    /**
     * @var string $basePath
     * Ruta base de l'aplicació, obtinguda automàticament
     */
    private string $basePath;

    private ?LoggerInterface $logger;

    /**
     * Constructor
     * Inicialitza el basePath automàticament segons la ubicació del script
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        // Obtenir el camí base de l'aplicació de forma automàtica
        $this->basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');   
        $this->logger = $logger;

        // LOG ROUTER INICIALITZAT - INFO
        if ($this->logger) {
            $this->logger->info('Router inicialitzat', [
                'basePath' => $this->basePath
            ]);
        }
    }

    /**
     * Registra una ruta GET
     * @param string $pattern URL amb paràmetres opcionals {id}
     * @param callable|string $callback Funció anònima o "Controlador@metode"
     */
    public function get(string $pattern, $callback): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'pattern' => $this->convertToRegex($pattern),
            'callback' => $callback
        ];
    }

    /**
     * Registra una ruta POST
     * @param string $pattern URL amb paràmetres opcionals {id}
     * @param callable|string $callback Funció anònima o "Controlador@metode"
     */
    public function post(string $pattern, $callback): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'pattern' => $this->convertToRegex($pattern),
            'callback' => $callback
        ];
    }

    /**
     * Converteix un patró de ruta amb {id} a una expressió regular
     * @param string $pattern Patró de ruta (ex: /tasques/{id})
     * @return string Regex equivalent per fer el match amb preg_match
     */
    private function convertToRegex(string $pattern): string
    {
        if ($pattern === '/') {
            $pattern = rtrim($pattern, '/');
        }
        
        // {id} - només números | cualsevol altre param. - alfanumèric + guións.
        $regex = preg_replace_callback(
            '#\{([a-zA-Z_]+)\}#',
            fn($matches) => $matches[1] === 'id' ? '([0-9]+)' : '([a-zA-Z-0-9_-]+)',
            $pattern
        );
        return "#^" . $regex . "$#";
    }

    /**
     * Despatxa la ruta corresponent segons la URI actual
     * @param string $uri La ruta sol·licitada pel navegador ($_SERVER['REQUEST_URI'])
     */
    public function dispatch(string $uri)
    {
        // Eliminar paràmetres GET i basePath
        $uri = parse_url($uri, PHP_URL_PATH);

        if ($this->basePath !== '' && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        // LOG DISPATCH INICIAT - INFO
        if ($this->logger) {
            $this->logger->info('Dispatch iniciat', [
                'request_URI' => $_SERVER['REQUEST_URI'],
                'parsed_URI'  => $uri,
                'method'      => $_SERVER['REQUEST_METHOD']
            ]);
        }
        
        $uri = $uri === '' ? '/' : rtrim($uri, '/');

        foreach ($this->routes as $route) {

            // Comprovar que el mètode coincideix
            if ($_SERVER['REQUEST_METHOD'] !== $route['method']) {
                continue;
            }

            // LOG COMPROVANT RUTA - INFO
            if ($this->logger) {
                $this->logger->info('Comprovant ruta', [
                    'method'  => $route['method'],
                    'pattern' => $route['pattern'],
                    'uri'     => $uri
                ]);
            }

            // Comprovar si la ruta coincideix amb la regex
            if (preg_match($route['pattern'], $uri, $matches)) {

                // LOG RUTA COINCICENT - INFO
                if ($this->logger) {
                    $this->logger->info('Ruta Coincident', [
                        'pattern' => $route['pattern'],
                        'uri'     => $uri,
                        'params'  => $matches
                    ]);
                }

                // Eliminem el match complet
                array_shift($matches);

                $callback = $route['callback'];

                // Forma "Controller@method" (per compatibilitat)
                if (is_string($callback) && strpos($callback, '@') !== false) {
                    [$controller, $method] = explode('@', $callback);

                    $controllerClass = 'App\\Http\\Controllers\\' . $controller;

                    if (!class_exists($controllerClass)) {
                        http_response_code(500);
                        echo "Controller $controllerClass no trobat.";
                        exit;
                    }

                    $controllerInstance = new $controllerClass();

                    if (!method_exists($controllerInstance, $method)) {
                        http_response_code(500);
                        echo "Mètode $method no trobat a $controllerClass.";
                        exit;
                    }

                    return $controllerInstance->$method(...$matches);
                }

                // Forma [Controller::class, 'method']
                if (is_array($callback) && count($callback) === 2) {
                    [$controllerClass, $method] = $callback;

                    if (!class_exists($controllerClass)) {
                        http_response_code(500);
                        echo "Controller $controllerClass no trobat.";
                        exit;
                    }

                    $controllerInstance = new $controllerClass();

                    if (!method_exists($controllerInstance, $method)) {
                        http_response_code(500);
                        echo "Mètode $method no trobat a $controllerClass.";
                        exit;
                    }

                    return $controllerInstance->$method(...$matches);
                }

                // Funcions anònimes o callables genèrics
                return call_user_func_array($callback, $matches);
            }
        }
        
        // LOG CAP RUTA COINCIDEIX - WARNING
        if ($this->logger) {
            $this->logger->warning('Cap ruta coincideix', [
                'uri'    => $uri,
                'method' => $_SERVER['REQUEST_METHOD'],
                'routes' => array_column($this->routes, 'pattern')
            ]);
        }
        // Si no trobem cap ruta → Error 404
        http_response_code(404);
        if (defined('APP_ROOT') && file_exists(APP_ROOT . '/src/Views/home/404.php')) {
            require APP_ROOT . '/src/Views/home/404.php';
        } else {
            echo '404 - Pàgina no trobada';
        }
        exit;
    } 
}