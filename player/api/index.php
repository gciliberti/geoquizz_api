<?php
require_once "../src/vendor/autoload.php";

use \Respect\Validation\Validator as v;
use \DavidePastore\Slim\Validation\Validation as Validation;
use geoquizz\app\database\DatabaseConnection;

$settings = require_once "../conf/settings.php";
$errorsHandlers = require_once "../conf/errorsHandlers.php";
$app_config = array_merge($settings, $errorsHandlers);

$container = new \Slim\Container($app_config);
$app = new \Slim\App($container);

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

//validators
$createPartyValidator = [
    'pseudo' => v::stringType(),
    'serie' => v::regex('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/'),
];

$updatePartyValidator = [
    'id' => v::intVal(),
    'score' => v::intVal(),
];

$app->get('/leaderboard[/]', geoquizz\app\control\PartieController::class . ':leaderboard');
$app->post('/partie[/]', geoquizz\app\control\PartieController::class . ':creerPartie')
    ->add( geoquizz\app\middleware\Validator::class . ":dataFormatErrorHandler")
    ->add(new Validation($createPartyValidator));

$app->get('/series[/]', geoquizz\app\control\SerieController::class . ':getSeries');
$app->patch('/partie/{token}[/]', geoquizz\app\control\PartieController::class . ':updatePartie')
    ->add( geoquizz\app\middleware\Validator::class . ":dataFormatErrorHandler")
    ->add(new Validation($updatePartyValidator));

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();