<?php

require_once "../src/vendor/autoload.php";

use geoquizz\app\database\DatabaseConnection;

use \Respect\Validation\Validator as v;
use \DavidePastore\Slim\Validation\Validation as Validation;


$settings = require_once "../conf/settings.php";
$errorsHandlers = require_once "../conf/errorsHandlers.php";
$app_config = array_merge($settings, $errorsHandlers);
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../src");
$dotenv->load();

$container = new \Slim\Container($app_config);
$app = new \Slim\App($container);
$postSerieValidator = [
    'ville' => v::stringType(),
    'map_refs' => v::numeric(),
    'dist' => v::numeric()->length(1, 1),
    "photos" => v::arrayType()
];

$updateSerieValidator = [
    'ville' => v::stringType()->alpha(),
    'map_refs' => v::optional(v::numeric()),
    'dist' => v::numeric()->length(1, 1),
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


$app->add(new Tuupola\Middleware\JwtAuthentication([
    "ignore" => ["/login", "/register"],
    "secret" => getenv("JWT_SECRET"),
]));

$app->post('/login[/]', geoquizz\app\control\ControllerUser::class . ':login');

$app->post('/register[/]', geoquizz\app\control\ControllerUser::class . ':register');

$app->get('/series[/]', geoquizz\app\control\SerieController::class . ':getSeries');
$app->post('/series/serie[/]', geoquizz\app\control\SerieController::class . ':createSerie')->add(new Validation($postSerieValidator));
$app->put('/series/serie/{id_serie}[/]', geoquizz\app\control\SerieController::class . ':updateSerie')->add(new Validation($updateSerieValidator));
$app->delete('/series/serie/{id_serie}[/]',geoquizz\app\control\SerieController::class . ':deleteSerie');


$app->get('/photos[/]', \geoquizz\app\control\PhotoController::class . ':getPhotos');
$app->get('/photos/{id_serie}', \geoquizz\app\control\PhotoController::class . ':getPhotosSerie');
$app->post('/photo/serie[/]', \geoquizz\app\control\PhotoController::class . ':postPhotosSerie');
$app->post('/photos/photo[/]', \geoquizz\app\control\PhotoController::class . ':postPhoto');
$app->delete('/photo/{id_photo}[/]', \geoquizz\app\control\PhotoController::class . ':deletePhoto');
$app->delete('/photo/serie/{id_serie}[/]', \geoquizz\app\control\PhotoController::class . ':deletePhotoFromSerie');

$app->get('/maps[/]', geoquizz\app\control\MapController::class . ':getMaps');
$app->post('/maps[/]', geoquizz\app\control\MapController::class . ':addMap');


$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();