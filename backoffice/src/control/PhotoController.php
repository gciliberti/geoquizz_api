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


class PhotoController
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
        if (isset($args['id_serie'])) {


            try {

                $serie = Serie::query()->where('id', '=', $args['id_serie'])->firstOrFail();
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


    public static function postPhotosSerie(Request $request, Response $response, $args)
    {
        $input = $request->getParsedBody();
        if (isset($input['photo_id']) && isset($input['serie_id'])) {
            try {

                //On enregistre l'image dans la bdd avec l'url généré par cloudinary
                $photoSerie = new photo_serie();
                $photoSerie->photo_id = filter_var($input['photo_id'], FILTER_SANITIZE_NUMBER_INT);
                $photoSerie->serie_id = filter_var($input['serie_id'], FILTER_SANITIZE_STRING);
                $photoSerie->saveOrFail();

                $element = [
                    "photo_id" => $photoSerie->photo_id,
                    "serie_id" => $photoSerie->serie_id,
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

    public static function deletePhoto(Request $request, Response $response, $args)
    {
        if (isset($args['id_photo'])) {
            try {
                $photo = Photo::query()->where('id', '=', $args['id_photo'])->firstOrFail();
                if ($photo_serie = Photo_Serie::query()->where('photo_id', $args['id_photo'])->delete() == true) {
                    $photo_serie = Photo_Serie::query()->where('photo_id', $args['id_photo'])->delete();
                }

            } catch (\Exception $e) {

                $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "Photo non trouvé"));
                return $response;
            }

            $photo->delete();

            $response->getBody()->write(json_encode([
                "type" => "success",
                "status" => 200,
                "message" => "photo supprimé"
            ]));
            return $response;


        } else {
            $response = $response->withStatus(400)->withHeader('Content-Type', 'application/json;charset=utf-8');
            $response->getBody()->write(json_encode([
                "type" => "error",
                "error" => 400,
                "message" => "veuillez passer l'identifiant d'une photo"
            ]));

        }

        return $response;
    }

    public static function deletePhotoFromSerie(Request $request, Response $response, $args)
    {
        if (isset($args['id_serie'])) {
            $body = $request->getParsedBody();


            try {
                    if ($photo_serie = Photo_Serie::query()->where('photo_id', '=', $body['photo_id'])->where('serie_id', '=', $args['id_serie'])->delete() == false) {
                        $response = Writer::jsonResponse($response, 404, array("error" => 404, "message" => "La ou les photos sont introuvable"));
                        return $response;
                    } else {
                        $photo_serie = Photo_Serie::query()->where('photo_id', '=', $body['photo_id'])->where('serie_id', '=', $args['id_serie'])->delete();
                    }

               

            } catch (\Exception $e) {


            }


            $response = Writer::jsonResponse($response, 404, array("type" => 'success', "status" => 200, "message" => "Les photos ont été supprimé de la sériee"));
            return $response;
        }
        return $response;
    }


}
