<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/bancossat'], function () use ($router) {
    $router->get('index', 'BancosSatController@index');
    $router->post('get-bancos-sat', 'BancosSatController@busqueda');
});

$router->group(['prefix' => 'api/empresas'], function () use ($router) {
    $router->get('index', 'EmpresasController@index');
    $router->post('get-empresas', 'EmpresasController@busqueda');
    $router->post('guardarempresas', 'EmpresasController@guardar');
    $router->put('{id}', 'EmpresasController@actualizar');
    $router->delete('{id}', 'EmpresasController@borrar');
});


$router->group(['prefix' => 'api/bancos'], function () use ($router) {
    $router->get('index', 'BancosController@index');
    $router->post('get-bancos', 'BancosController@busqueda');
    $router->post('guardarbancos', 'BancosController@guardar');
    $router->put('{id}', 'BancosController@actualizar');
    $router->delete('{id}', 'BancosController@borrar');
});

$router->group(['prefix' => 'api/movbancos'], function () use ($router) {
    $router->get('index', 'MovBancosController@index');
    $router->post('get-movbancos', 'MovBancosController@busqueda');
    $router->post('guardarmovcuentas', 'MovBancosController@guardarMovCuentas');
    $router->put('{id}', 'MovBancosController@actualizarMovCuentas');
    $router->delete('{id}', 'MovBancosController@borrar');
});

$router->group(['prefix' => 'api/conceptos'], function () use ($router) {
    $router->get('index', 'ConceptosController@index');
    $router->post('get-conceptos', 'ConceptosController@busqueda');
    $router->post('guardarconceptos', 'ConceptosController@guardar');
    $router->put('{id}', 'ConceptosController@actualizar');
    $router->delete('{id}', 'ConceptosController@borrar');
});