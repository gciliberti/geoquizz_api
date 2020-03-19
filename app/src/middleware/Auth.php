<?php


namespace geoquizz\app\middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \DomainException;
use geoquizz\app\utils\Writer;

class Auth
{
    public function __construct(\Slim\Container $container = null) {
        $this->container = $container;
    }


}