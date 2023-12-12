<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->get('/', 'Importacao::index');
$routes->post('/importacao/processar', 'Importacao::processar');
$routes->post('/importacao/dat_analitico', 'Importacao::dat_analitico');
$routes->post('/importacao/agrupar_descontos', 'Importacao::agrupar_descontos');
$routes->get('/importacao/listar_agrupado/(:segment)', 'Importacao::listar_agrupado/$1');