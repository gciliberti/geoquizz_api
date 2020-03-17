<?php
namespace geoquizz\app\utils;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\model\Commande;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Middleware {
    public static function validateToken(Request $request, Response $response, callable $next) {
        $token = $request->getQueryParam('token', null);

        if (!isset($token)) {
            if ($request->hasHeader('X-lbs-token')) {
                $token = $request->getHeader('X-lbs-token')[0];
            }
        }

        $id = $request->getAttribute('route')->getArgument('id');
        $command = null;

        try {
            $command = Commande::query()->where('id', '=', $id)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return Writer::jsonResponse($response, 404, [
                "type" => "error",
                "error" => 404,
                "message" => "Commande introuvable."
            ]);
        }

        if ($token !== $command->token) {
            return Writer::jsonResponse($response, 401, [
                "type" => "error",
                "error" => 401,
                "message" => "Token invalide, accès interdit."
            ]);
        }

        $request = $request->withAttribute('command', $command);

        return $next($request, $response);
    }

    public static function dataFormatErrorHandler(Request $request, Response $response, callable $next) {
        if ($request->getAttribute('has_errors')) {
            return Writer::jsonResponse($response, 400, [
                "type" => "error",
                "error" => 400,
                "message" => "Paramètres mal formés."
            ]);
        }

        return $next($request, $response);
    }
}