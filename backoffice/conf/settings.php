<?php
use Respect\Validation\Validator;

return [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => '../conf/config.ini',
        'validatorRegister' => [
            'nom' => Validator::alpha("é à è ù ô î â ï ë ö ü"),
            'prenom' =>  Validator::alpha("é à è ù ô î â ï ë ö ü"),
            'mail' =>  Validator::email(),
            'motdepasse' =>  Validator::stringType()->length(4, null),
            'telephone' =>  Validator::phone(),
        ],
        'postSerieValidator' => [
            'ville' => Validator::stringType(),
            'map_refs' => Validator::numeric(),
            'dist' => Validator::numeric(),
            "photos" => Validator::arrayType(),
            'photos_jouables' => Validator::intVal(),
        ],
        'updateSerieValidator'=> [
            'ville' => Validator::stringType()->alpha(),
            'map_refs' => Validator::optional(Validator::numeric()),
            'dist' => Validator::numeric()->length(1, 1),
        ],
        'deleteSerieValidator'=> Validator::regex('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/'),
        'photoSerieValidator'=>[
            "photo_id" =>Validator::intVal(),
            "serie_id" =>Validator::regex('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/')
        ],
        'postPhotoValidator'=>[
            "photo"=> Validator::stringType(),
            "description"=> Validator::alnum("é à è ù ô î â ï ë ö ü , : ) ( '"),
            "localisation"=> Validator::alnum(", ."),
        ]
    ]
];