<?php

require_once "../src/vendor/autoload.php";

use geoquizz\app\database\DatabaseConnection;

use \Respect\Validation\Validator as v;
use \DavidePastore\Slim\Validation\Validation as Validation;


$settings = require_once "../conf/settings.php";
$errorsHandlers = require_once "../conf/errorsHandlers.php";
$app_config = array_merge($settings, $errorsHandlers);

$container = new \Slim\Container($app_config);
$app = new \Slim\App($container);
$postSerieValidator = [
    'ville' => v::stringType(),
    'map_refs' => v::numeric(),
    'dist' => v::numeric()->length(1, 1),
    "photos" => v::arrayType()
];
DatabaseConnection::startEloquent(($app->getContainer())->settings['dbconf']);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});


$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/series[/]', geoquizz\app\control\SerieController::class . ':getSeries');
$app->post('/series/serie[/]', geoquizz\app\control\SerieController::class . ':createSerie')->add(new Validation($postSerieValidator));

$app->get('/photos[/]', \geoquizz\app\control\Photocontroller::class . ':getPhotos');
$app->get('/photos/{id}', \geoquizz\app\control\Photocontroller::class . ':getPhotosSerie');
$app->post('/photos/photo[/]', \geoquizz\app\control\Photocontroller::class . ':postPhoto');


$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();