<?php
/**
 * index.php
 * 
 * Punt d'entrada principal de l'aplicació MVC.
 * Gestiona totes les rutes a través del Router i crida als controladors corresponents.
 */

require_once 'Router.php'; // Incloem la classe Router

// Instanciem el Router
$router = new Router();

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
 * Ruta de prova amb funció anònima
 * Serveix per comprovar que el Router captura correctament els paràmetres dinàmics
 */
$router->get("/tasques/{id}", function($id) {
    echo "Has demanat la tasca amb ID: " . $id;
});

/**
 * Despatxar la ruta actual
 * Passa la URI del navegador al Router per gestionar la ruta corresponent
 */
$router->dispatch($_SERVER['REQUEST_URI']);

?>