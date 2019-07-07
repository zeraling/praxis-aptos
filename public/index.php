<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

//incluye la carga automatica de dependencias 
(file_exists('../vendor/autoload.php')) ? require_once '../vendor/autoload.php' : die('Dependencias no Cargadas!');

use Aura\Router\RouterContainer;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\HttpHandlerRunnerMiddleware;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use App\Connections\DataHotelDB;
use App\Core\Utils;

define('BASE_URL', Utils::baseUrl());

try {
    
    // carga el archivo de entorno
    $dotenv = \Dotenv\Dotenv::create(__DIR__ . '/..');
    $dotenv->load();
    
//se define un controlador de rutas
    $routerContainer = new RouterContainer();
// se define una variable para almacenar el mapa de rutas
    $map = $routerContainer->getMap();

    // se cargan las rutas de la aplicacion
    foreach (glob("../routes/*.php") as $filename) {
        require_once $filename;
    }
    
    $matcher = $routerContainer->getMatcher();

//se define la variable que almacenara todas las petiviones que se hagan
    $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

    $route = $matcher->match($request);

    if (!$route) {
        $emitter = new SapiEmitter();
        $emitter->emit(new Response\EmptyResponse(404));
    } else {
        
        //Boot Database Connection
        new DataHotelDB();
        
        try {
            $harmony = new Harmony($request, new Response());
            $harmony->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()));

            $harmony->addMiddleware(new Franzl\Middleware\Whoops\WhoopsMiddleware());
            $harmony->addMiddleware(new App\Core\Middlewares\AuthenticationMiddleware());
            $harmony->addMiddleware(new Middlewares\AuraRouter($routerContainer));
            $harmony->addMiddleware(new DispatcherMiddleware(null, 'request-handler'));

            $harmony();
        } catch (Exception $e) {

            var_dump($e);

            echo 's';

            //$emitter = new SapiEmitter();
            //$emitter->emit(new Response\EmptyResponse(400));
        } catch (Error $e) {

            var_dump($e);

            echo 'cc';
            //$emitter = new SapiEmitter();
            //$emitter->emit(new Response\EmptyResponse(500));
        }
    }
} catch (Dotenv\Exception\InvalidPathException $exc) {
    $emitter = new SapiEmitter();
    $emitter->emit(new Response\EmptyResponse(500));
}
