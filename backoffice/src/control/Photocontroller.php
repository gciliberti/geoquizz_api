<?php

namespace geoquizz\app\control;

use \Firebase\JWT\JWT;

use geoquizz\app\model\Photo;
use geoquizz\app\model\Photo_Serie;
use geoquizz\app\model\Serie;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use geoquizz\app\utils\Writer;
use Slim\Http\Request;
use Slim\Http\Response;


class Photocontroller
{
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public static function getPhotos(Request $request, Response $response, $args)
    {
        $photos = Photo::query()->get();
        $resparray = [
            "photos" => $photos
        ];
        $response = Writer::jsonResponse($response, 200, $resparray);
    }


    public static function getPhotosSerie(Request $request, Response $response, $args)
    {
        if (isset($args['id'])) {


            try {

                $serie = Serie::findOrFail($args["id"]);
                $photos = $serie->photos()->get();
                foreach ($photos as $photo) {
                    unset($photo["pivot"]);
                    unset($photo["created_at"]);
                }

                $resparray = [
                    "photos" => $photos
                ];

                $response = Writer::jsonResponse($response, 200, $resparray);

            } catch (\Exception $e) {
                $resparray = array(
                    "error" => 500,
                    "message" => "L'identifiant transmis n'éxste pas",
                );

                $response = Writer::jsonResponse($response, 500, $resparray);
            }


        }

    }

    public static function postPhoto(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        if (isset($input['photo']) && isset($input['localisation']) && isset($input['description'])) {
            try {
                //Appel api cloudinary
                \Cloudinary::config(array(
                    "cloud_name" => 'dw3pqqmbc',
                    "api_key" => '325992955685386',
                    "api_secret" => 'RA9RUeFJl0ulqII22HSdavdDsgc'
                ));
                $img = "data:image/jpeg;base64," . $input['photo'];
                $arr_result = \Cloudinary\Uploader::upload($img);

                //On enregistre l'image dans la bdd avec l'url généré par cloudinary
                $photo = new photo();
                $photo->desc = $input['description'];
                $photo->position = $input['localisation'];
                $photo->url = $arr_result['url'];
                $photo->saveOrFail();

                $element = [
                    "id" => $photo->id,
                    "desc" => $photo->desc,
                    "position" => $photo->position,
                    "url" => $arr_result['url'],
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


}