<?php
namespace geoquizz\app\utils;

use Carbon\Carbon;
use \Psr\Http\Message\ResponseInterface as Response;

class Writer {
    public static function jsonResponse(Response $response, $status, $json_array) {
        $response = $response->withStatus($status)
            ->withHeader("Content-Type", "application/json;charset=utf-8");

        $response->getBody()
            ->write(json_encode($json_array));

        return $response;
    }

    public static function dateToString($date_array, $format) {
        $date_string = $date_array['date']." ".$date_array['heure'];
        return date($format,strtotime($date_string));
    }

    public static function dateToArray($date_string) {
        $date = Carbon::parse($date_string);
        return [
            "date" => $date->year."-".$date->month."-".$date->day,
            "heure" => $date->hour.":".$date->minute.":".$date->second
        ];
    }

    public static function generateToken() {
        $token = openssl_random_pseudo_bytes(32);
        $token = bin2hex($token);
        return $token;
    }
}