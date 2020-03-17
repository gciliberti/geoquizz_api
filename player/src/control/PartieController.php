<?php
namespace geoquizz\app\control;

use geoquizz\app\model\Serie;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use geoquizz\app\model\Partie;
use geoquizz\app\utils\Writer;
use \Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class PartieController {
    const CREEE = 0;
    const EN_COURS = 1;
    const TERMINEE = 2;
    protected $container;

    public function __construct(\Slim\Container $container = null) {
        $this->container = $container;
    }
    public function creerPartie(Request $request, Response $response, $args) {
        try{

            $contenu = $request->getParsedBody();
            $serie = Serie::findOrFail($contenu["serie"]);
            $photos = $serie->photos()->get();
            $partie = new Partie();
            $partie->token = Writer::generateToken();
            $partie->nb_photos = 0;
            $partie->status = self::EN_COURS;
            $partie->score = 0;
            $partie->joueur = $contenu["pseudo"];

            foreach ($photos as $photo){
                unset($photo["pivot"]);
                unset($photo["created_at"]);
            }

            $partie->saveOrFail();



            $resparray = array(
                "token" => $partie->token,
                "status" => $partie->status,
                "photos" => $photos,
            );

            $response = Writer::jsonResponse($response,201,$resparray);

        } catch (\Exception $e){
            $resparray = array(
                "error" => 500,
                "message" => $e->getMessage(),

            );

            $response = Writer::jsonResponse($response,500,$resparray);
        }


        return $response;
    }
}