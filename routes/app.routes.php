<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// pagina principal de la aplicacion
$map->get('index', '/', ['App\Controllers\HomeController', 'getHome']);
$map->get('lista.aptos', '/aptos', ['App\Controllers\HomeController', 'getApartamentos']);
$map->get('lista.visitas', '/visitas', ['App\Controllers\HomeController', 'getVisitas']);

// propietarios del apartamento
$map->get('info.aptos', '/aptos/{id}/info', ['App\Controllers\ApartamentosController', 'getPropietarios']);

// administracion de propietarios de apartamentos
$map->get('add.propietario', '/aptos/{id}/admin/{/propietario}', ['App\Controllers\ApartamentosController', 'getPropietarioApto']);
$map->post('edit.propietario', '/aptos/{id}/admin/{/propietario}', ['App\Controllers\ApartamentosController', 'postPropietariosApto']);
$map->get('delete.propietario', '/aptos/{id}/admin/{propietario}/delete', ['App\Controllers\AdminController', 'deletePropietariosApto']);


// lista de visitas por apartamento

$map->get('visitas.apto', '/visitas/{apto}', ['App\Controllers\HotelesController', 'getHoteles']);

// registro de visitas a apartamentos
$map->get('add.visita', '/visitas/{apto}/form{/id}', ['App\Controllers\HotelesController', 'getHoteles']);
$map->post('edit.visita', '/visitas/{apto}/form{/id}', ['App\Controllers\HotelesController', 'getHoteles']);
$map->get('delete.visita', '/visitas/{apto}/{visita}/delete', ['App\Controllers\HotelesController', 'getHoteles']);
