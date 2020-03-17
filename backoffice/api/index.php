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

DatabaseConnection::startEloquent(($app->getContainer())->settings['dbconf']);

$postSerieValidator = [
    'ville' => v::stringType(),
    'map_refs' =>v::numeric(),
    'dist' =>v::numeric()->length(1, 1),
    "photos" => v::arrayType()
];


$app->get('/series[/]', geoquizz\app\control\SerieController::class . ':getSeries');
$app->post('/series/serie[/]', geoquizz\app\control\SerieController::class . ':createSerie')->add(new Validation($postSerieValidator));

$app->get('/photos[/]', \geoquizz\app\control\Photocontroller::class . ':getPhotos');
$app->post('/photos/photo[/]',\geoquizz\app\control\Photocontroller::class . ':postPhoto' );





$app->run();