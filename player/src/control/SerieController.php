<?php


namespace geoquizz\app\control;

use geoquizz\app\model\Serie;
use Illuminate\Database\Eloquent\Model;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use geoquizz\app\model\Partie;
use geoquizz\app\utils\Writer;
use \Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class SerieController
{
    protected $container;

    public function __construct(\Slim\Container $container = null)
    {
        $this->container = $container;
    }

    public function getSeries(Request $request, Response $response, $args)
    {
        try {
            $series = Serie::orderBy("ville")->get();
            $seriesarray = array();

            foreach ($series as $serie){
                array_push($seriesarray,$serie);
            }

            $resparray = array(
                "series" => $seriesarray

            );

            $response = Writer::jsonResponse($response, 200, $resparray);

        } catch (\Exception $e) {
            $resparray = array(
                "error" => 500,
                "message" => "internal server error",
            );

            $response = Writer::jsonResponse($response, 500, $resparray);
        }


        return $response;
    }
}