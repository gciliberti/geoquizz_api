<?php
require_once "../src/vendor/autoload.php";

use DavidePastore\Slim\Validation\Validation;
use geoquizz\app\database\DatabaseConnection;

$settings = require_once "../conf/settings.php";
$errorsHandlers = require_once "../conf/errorsHandlers.php";
$app_config = array_merge($settings, $errorsHandlers);

$container = new \Slim\Container($app_config);
$app = new \Slim\App($container);

DatabaseConnection::startEloquent(($app->getContainer())->settings['dbconf']);

$app->post('/partie[/]', geoquizz\app\control\PartieController::class . ':creerPartie');
$app->get('/series[/]', geoquizz\app\control\SerieController::class . ':getSeries');


$app->run();