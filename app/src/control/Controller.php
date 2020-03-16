<?php
namespace geoquizz\app\control;

use \Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

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
}