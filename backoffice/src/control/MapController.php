<?php

namespace geoquizz\app\control;

use geoquizz\app\model\Map;
use geoquizz\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;

class MapController
{
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public function getMaps(Request $request, Response $response, $args)
    {
        try {
            $maps = Map::orderBy("ville")->get();
            $mapsarray = array();

            foreach ($maps as $map) {
                array_push($mapsarray, $map);
            }

            $resparray = array(
                "maps" => $mapsarray
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

    public function addMap(Request $request, Response $response, $args)
    {//Ajoute une map
        $input = $request->getParsedBody();
        if (isset($input['lat']) && isset($input['lng']) && isset($input['zoom']) && isset($input['ville']) && isset($input['miniature'])) {
            try {
                //Appel api cloudinary
                \Cloudinary::config(array(
                    "cloud_name" => 'dw3pqqmbc',
                    "api_key" => '325992955685386',
                    "api_secret" => 'RA9RUeFJl0ulqII22HSdavdDsgc'
                ));
                
                $imageUrl = file_get_contents($input['miniature']);
                $img = 'data:image/jpeg;base64,' . base64_encode($imageUrl);
                $arr_result = \Cloudinary\Uploader::upload($img);

                $map = new Map();
                $map->lat = filter_var($input['lat'], FILTER_SANITIZE_STRING);
                $map->lng = filter_var($input['lng'], FILTER_SANITIZE_STRING);
                $map->zoom = filter_var($input['zoom'], FILTER_SANITIZE_NUMBER_INT);
                $map->ville = filter_var($input['ville'], FILTER_SANITIZE_STRING);
                $map->miniature = $arr_result['url'];
                $map->saveOrFail();

                $element = [
                    "lat" => $map->lat,
                    "lng" => $map->lng,
                    "zoom" => $map->zoom,
                    "ville" => $map->ville,
                    "miniature" => $map->miniature,
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
        return $response;
    }
    public function deleteMap(Request $request, Response $response, $args){
        try {
            $map = Map::findOrFail($args["id_map"]);
            $series = $map->series();
            $series->delete();
            $map->delete();
            $response = Writer::jsonResponse($response, 200, array("success" => 200, "message" => "Map supprimée"));
            return $response;

        } catch (\Exception $e) {
            $response = Writer::jsonResponse($response, 500, array("error" => 404, "message" => "Map inexistante"));
            return $response;
        }
    }
}
