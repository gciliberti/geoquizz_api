<?php
namespace geoquizz\app\control;

use \Firebase\JWT\JWT;
use geoquizz\app\model\Serie;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use geoquizz\app\utils\Writer;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller {
    protected $container;

    public function __construct(\Slim\Container $container = null) {
        $this->container = $container;
    }
/**Exemple
    public static function getCommands(Request $request, Response $response, $args) {
        $commands = Commande::query()->get();

        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json;charset=utf-8');
        $response->getBody()->write(json_encode([
            "type" => "collection",
            "count" => count($commands),
            "commands" => $commands
        ]));

        return $response;
    }
 **/

    public static function getSeries(Request $request, Response $response, $args) {
        $series =Serie::query()->get();

//        $response = $response->withStatus(200)->withHeader('Content-Type', 'application/json;charset=utf-8');
//        $response->getBody()->write(json_encode([
//            "type" => "collection",
//            "count" => count($series),
//            "series" => $series
//        ]));

        $element = [
            "series" => $series
        ];
        $response = Writer::jsonResponse($response, 200, $element);
//        var_dump($response);

    }

}