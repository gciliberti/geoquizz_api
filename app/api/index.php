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
/**Exemple
$app->get('/commandes[/]', lbs\command\control\PartieController::class . ':getCommands');
**/

$app->post('/photo[/]', geoquizz\app\control\ControllerPhoto::class . ':addPhoto');

$app->post('/photo/serie[/]', geoquizz\app\control\ControllerPhoto::class . ':addPhotoSerie');

$app->get('/series[/]', geoquizz\app\control\ControllerSerie::class . ':getSeries');

$app->post('/series[/]', geoquizz\app\control\ControllerSerie::class . ':addSerie');

$app->get('/maps[/]', geoquizz\app\control\ControllerMap::class . ':getMaps');

$app->post('/maps[/]', geoquizz\app\control\ControllerMap::class . ':addMap');

$app->run();