<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->get('/', 'LoginController::index');
$routes->get('/importacao', 'Importacao::index');
$routes->post('/importacao/processar', 'Importacao::processar');
$routes->post('/importacao/dat_analitico', 'Importacao::dat_analitico');
$routes->get('/importacao/excluir_lancamentos/(:segment)', 'Importacao::excluir_lancamentos/$1');

$routes->get('/exportacao/gerar_xml/(:segment)', 'Exportacao::gerar_xml/$1');

$routes->get('/lista/agrupado', 'Lista::agrupado');
$routes->get('/lista/agrupado/(:segment)', 'Lista::agrupado/$1');
$routes->get('/lista', 'Lista::index');
$routes->get('/lista/centro_custo/(:segment)', 'Lista::centro_custo/$1');
$routes->get('/verba_mes/(:segment)/(:segment)/(:segment)', 'Lista::verba_mes/$1/$2/$3');
$routes->get('contracheque/(:segment)/(:segment)', 'Lista::contracheque/$1/$2');

$routes->get('/lista/empenhos/(:segment)', 'Lista::empenhos/$1');

$routes->get('/lista/encargos/(:segment)', 'Lista::encargos/$1');
$routes->get('/encargos/cadastrar/(:segment)', 'Lista::cadastrarEncargos/$1');
$routes->post('/encargos/adicionar', 'Lista::adicionarEncargos');

$routes->get('/grupos', 'Grupo::index');
$routes->get('/grupos/(:segment)', 'Grupo::index/$1');
$routes->post('/cadastrar_grupo', 'Grupo::cadastrar');
$routes->get('/excluir_grupo/(:segment)', 'Grupo::excluir/$1');
$routes->get('/desvincular/(:segment)', 'Grupo::desvincular/$1');
$routes->get('/desvincular/(:segment)/(:segment)', 'Grupo::desvincular/$1/$2');
$routes->post('/vincular', 'Grupo::vincular');

$routes->get('/login', 'LoginController::index');
$routes->post('/login', 'LoginController::autenticar');
$routes->get('/login/usuario_invalido', 'LoginController::index/51');
$routes->get('/login/senha_invalida', 'LoginController::index/52');
$routes->get('/logout', 'LoginController::logout');