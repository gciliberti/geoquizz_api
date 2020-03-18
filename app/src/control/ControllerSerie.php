<?php
namespace geoquizz\app\control;

use geoquizz\app\model\serie;
use geoquizz\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;

class ControllerSerie
{
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public function getSeries(Request $request, Response $response, $args)
    {
        try {
            $series = serie::orderBy("ville")->get();
            $seriesarray = array();

            foreach ($series as $serie) {
                array_push($seriesarray, $serie);
            }

            $resparray = array(
                "series" => $seriesarray

            );

            $response = Writer::jsonResponse($response, 200, $resparray);

        } catch (\Exception $e) {
            $resparray = array(
                "error" => 500,
                "message" => var_dump($e->getMessage()),
            );

            $response = Writer::jsonResponse($response, 500, $resparray);
        }


        return $response;
    }

    public function addSerie(Request $request, Response $response, $args){//Ajoute une serie sans photo
        $input = $request->getParsedBody();
        if (isset($input['ville']) && isset($input['map_refs']) && isset($input['dist'])) {
            try {
                $serie = new serie();
                $serie->id = Uuid::uuid4();
                $serie->ville = filter_var($input['ville'], FILTER_SANITIZE_STRING);
                $serie->map_refs = filter_var($input['map_refs'], FILTER_SANITIZE_STRING);
                $serie->dist = filter_var($input['dist'], FILTER_SANITIZE_NUMBER_INT);
                $serie->saveOrFail();

                $element = [
                    "id" => $serie->id,
                    "ville" => $serie->ville,
                    "map_refs" => $serie->map_refs,
                    "dist" => $serie->dist,
                ];
                $response = Writer::jsonResponse($response, 200, $element);

            } catch (\Throwable $exception) {
                $response = $response->withStatus(500)->withHeader('Content-Type', 'application/json;charset=utf-8');
                $response->getBody()->write(json_encode([
                    "type" => "error",
                    "error" => 500,
                    "message" => $exception->getMessage()
                ]));
            }
        } else {
            $response = $response->withStatus(400)->withHeader('Content-Type', 'application/json;charset=utf-8');
            $response->getBody()->write(json_encode([
                "type" => "error",
                "error" => 400,
                "message" => "Au moins un champ n'a pas été rempli."
            ]));
        }
    }
}