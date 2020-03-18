<?php

namespace geoquizz\app\control;

use \Firebase\JWT\JWT;
use geoquizz\app\model\Serie;
use geoquizz\app\model\Photo_Serie;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use geoquizz\app\utils\Writer;
use Slim\Http\Request;
use Slim\Http\Response;

class SerieController
{
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public static function getSeries(Request $request, Response $response, $args)
    {
        $series = Serie::query()->get();
        $resparray = [
            "series" => $series
        ];
        $response = Writer::jsonResponse($response, 200, $resparray);
    }

    public static function createSerie(Request $request, Response $response, $args)
    {

        if ($request->getAttribute('has_errors')) {
//            $errors = $request->getAttribute('errors');
//            foreach ($errors as $key => $listerrorAttribute) {
//                echo "<strong>" . $key . " : </strong>  ";
//                //echo "<br/>";
//                foreach ($listerrorAttribute as $error) {
//                    echo $error;
//                    echo "<br/>";
//                }
//            }

            $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Merci de transmettre des données valide"));
            return $response;

        } else {

            $req_body = $request->getBody()->getContents();
            $body = json_decode($req_body, true);

            try {
                $uuid = Uuid::uuid4();
            } catch (\Exception $e) {
                echo $e;
            }
            $serie = new Serie();
            $serie->id = $uuid->toString();
            $serie->ville = filter_var($body['ville'], FILTER_SANITIZE_STRING);
            $serie->map_refs = filter_var($body['map_refs'], FILTER_SANITIZE_NUMBER_INT);
            $serie->dist = filter_var($body['dist'], FILTER_SANITIZE_NUMBER_INT);
            $serie->save();

            if (isset($serie)) {
                //pour remplir la table pivot
                foreach ($body['photos'] as $photo) {
                    foreach ($photo as $photo_id) {
                        $photo_serie = new Photo_Serie();
                        $photo_serie->photo_id = $photo_id;
                        $photo_serie->serie_id = $serie->id;
                        $photo_serie->save();

                    }
                };

            }


            $resparray = [
                "series" => $serie,
                "photo_serie" => $photo_serie
            ];
            $response = Writer::jsonResponse($response, 200, $resparray);

        }
    }

    public static function updateSerie(Request $request, Response $response, $args)
    {

        if ($request->getAttribute('has_errors')) {

            $errors = $request->getAttribute('errors');
            var_dump($errors);


        } else {
            $body = $request->getParsedBody();
            if (isset($args['id_serie'])) {
                try {
                    $serie = Serie::query()->where('id', '=', $args['id_serie'])->firstOrFail();

                } catch (\Exception $e) {
                    $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Serie non trouvé"));
                    return $response;
                }


                if (isset($body['ville'])) {
                    $serie->ville = filter_var($body['ville'], FILTER_SANITIZE_STRING);

                };


                if (isset($body['dist'])) {
                    $serie->dist = filter_var($body['dist'], FILTER_SANITIZE_NUMBER_INT);
                };

                $serie->saveOrFail();


                $resparray = [
                    "serie" => $serie,
                ];
                $response = Writer::jsonResponse($response, 200, $resparray);
                return $response;


            } else {
                $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Merci de transmettre des données valide"));
                return $response;
            }


//            if (isset($body["id"]) && isset($body["score"])) {
//                try {
//                    if ($partie->status == self::EN_COURS) {
////                        $partie->score = $input["score"];
////                        $partie->status = self::TERMINEE;
////                        $partie->saveOrFail();
//
//                        $response = Writer::jsonResponse($response, 204, array("error" => 204, "message" => "Score sauvegardé"));
//                        return $response;
//                    } else {
//                        throw new Exception('Partie terminée');
//                    }
//                } catch (\Exception $e) {
//                    $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Partie non trouvé"));
//                    return $response;
//                }
//            }

        }
    }


}