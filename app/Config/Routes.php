<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');

$routes->get('/', 'Importacao::index');
$routes->post('/importacao/processar', 'Importacao::processar');
$routes->post('/importacao/dat_analitico', 'Importacao::dat_analitico');



$routes->get('/exportacao/gerar_xml/(:segment)', 'Exportacao::gerar_xml/$1');

$routes->get('/lista/agrupado', 'Lista::agrupado');
$routes->get('/lista/agrupado/(:segment)', 'Lista::agrupado/$1');
$routes->get('/lista', 'Lista::index');
$routes->get('/lista/centro_custo/(:segment)', 'Lista::centro_custo/$1');
$routes->get('/lista/grupoCcusto/(:segment)', 'Lista::grupoCcusto/$1');

$routes->get('/lista/encargos/(:segment)', 'Lista::encargos/$1');
$routes->get('/encargos/cadastrar/(:segment)', 'Lista::cadastrarEncargos/$1');
$routes->post('/encargos/adicionar', 'Lista::adicionarEncargos');