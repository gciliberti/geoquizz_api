<?php

namespace geoquizz\app\control;

use geoquizz\app\model\Serie;
use mysql_xdevapi\Exception;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use geoquizz\app\model\Partie;
use geoquizz\app\utils\Writer;
use \Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class PartieController
{
    const CREEE = 0;
    const EN_COURS = 1;
    const TERMINEE = 2;
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public function leaderboard(Request $request, Response $response, $args)
    {
        $parties = Partie::orderBy('score','desc')->limit('10')->get();
        foreach ($parties as $partie) {
            unset($partie["id"]);
            unset($partie["token"]);
            unset($partie["nb_photos"]);
            unset($partie["status"]);
        }

        $resparray = array(
            "scores" => $parties,
        );
        $response = Writer::jsonResponse($response, 200, $resparray);
        return $response;
    }

    public function creerPartie(Request $request, Response $response, $args)
    {
        try {

            $contenu = $request->getParsedBody();
            $serie = Serie::findOrFail($contenu["serie"]);
            $photos = $serie->photos()->inRandomOrder()->take($serie->photos_jouables)->get();
            $partie = new Partie();
            $partie->token = Writer::generateToken();
            $partie->nb_photos = $serie->photos_jouables;
            $partie->status = self::EN_COURS;
            $partie->score = 0;
            $partie->joueur = $contenu["pseudo"];

            foreach ($photos as $photo) {
                unset($photo["pivot"]);
                unset($photo["created_at"]);
            }

            $partie->saveOrFail();


            $resparray = array(
                "id" => $partie->id,
                "nb_photos" => $partie->nb_photos,
                "token" => $partie->token,
                "status" => $partie->status,
                "map"=>$serie->map()->get(),
                "photos" => $photos,
                "serie" => $serie,
            );

            $response = Writer::jsonResponse($response, 201, $resparray);

        } catch (\Exception $e) {
            $resparray = array(
                "error" => 500,
                "message" => $e->getMessage(),

            );

            $response = Writer::jsonResponse($response, 500, $resparray);
        }


        return $response;
    }

    public function updatePartie(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();

        try {
            $partie = Partie::query()->where('id', '=', $input["id"])->firstOrFail();
        } catch (\Exception $e) {
            $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Partie non trouvé"));
            return $response;
        }

        if (isset($input["id"]) && isset($input["score"]) && $args["token"] == $partie->token) {
            try {
                if ($partie->status == self::EN_COURS) {
                    $partie->score = $input["score"];
                    $partie->status = self::TERMINEE;
                    $partie->saveOrFail();

                    $response = Writer::jsonResponse($response, 204, array("error" => 204, "message" => "Score sauvegardé"));
                    return $response;
                } else {
                    throw new Exception('Partie terminée');
                }
            } catch (\Exception $e) {
                $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Partie non trouvé"));
                return $response;
            }
        }
    }
}