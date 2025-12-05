<?php
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

    /**
     * Constructor
     * Inicialitza el basePath automàticament segons la ubicació del script
     */
    public function __construct()
    {
        // Obtenir el camí base de l'aplicació de forma automàtica
        $this->basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');   
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
        // Substituïm els paràmetres dinàmics per grups de captura numèrics
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '([0-9]+)', $pattern);
        return "#^" . $this->basePath . $regex . "$#";
    }

    /**
     * Despatxa la ruta corresponent segons la URI actual
     * @param string $uri La ruta sol·licitada pel navegador ($_SERVER['REQUEST_URI'])
     */
    public function dispatch(string $uri)
    {
        // Eliminar paràmetres GET i basePath
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {

            // Comprovar que el mètode coincideix
            if ($_SERVER['REQUEST_METHOD'] !== $route['method']) {
                continue;
            }

            // Comprovar si la ruta coincideix amb la regex
            if (preg_match($route['pattern'], $uri, $matches)) {

                // Eliminem el match complet
                array_shift($matches);

                // Si és un controlador i mètode ("Controller@method")
                if (
                    is_string($route['callback']) 
                    && 
                    strpos($route['callback'], '@') !== false
                ) {
                    list($controller, $method) = explode('@', $route['callback']);

                    $controllerFile = 'controllers/' . $controller . '.php';

                    // Comprovem que el fitxer existeix
                    if (!file_exists($controllerFile)) {
                        http_response_code(500);
                        echo "Controller $controller no trobat.";
                        exit;
                    }

                    require_once $controllerFile;

                    // Instanciem el controlador i cridem el mètode amb els paràmetres capturats
                    $instance = new $controller();
                    return call_user_func_array([$instance, $method], $matches);
                }
                
                // Si és una funció anònima, la cridem amb els paràmetres capturats
                return call_user_func_array($route['callback'], $matches);
            }
        }
    
        // Si no trobem cap ruta → Error 404
        http_response_code(404);
        require "views/home/404.php";
        exit;
    } 
}
?>