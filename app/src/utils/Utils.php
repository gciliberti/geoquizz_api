<?php
namespace lbs\command\utils;

use GuzzleHttp\Client;

class Utils {
    public static function calculateSumItems($items_tab) {
        $guzzle = new Client([
            'base_uri' => 'http://api.catalogue.local',
        ]);

        $sum = 0;

        foreach ($items_tab as $item) {
            $response = $guzzle->get($item['uri']);
            $sandwich = json_decode($response->getBody())->sandwich;

            $sum += $sandwich->prix*$item['q'];
        }

        return $sum;
    }

    public static function getSandwichInfo($uri) {
        $guzzle = new Client([
            'base_uri' => 'http://api.catalogue.local',
        ]);

        $response = $guzzle->get($uri);
        return json_decode($response->getBody())->sandwich;
    }
}