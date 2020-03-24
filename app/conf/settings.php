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
        'validatorAddPhoto' => [
            'localisation' => Validator::alnum(", ."),
            'description' =>  Validator::alnum("é à è ù ô î â ï ë ö ü , : '"),
            'photo' =>  Validator::stringType(),//remplacer par base64() lors de la maj de Validator/Validate
        ],
        'validatorlinkPhotoSeries' => [
            'photo_id' => Validator::intVal(),
            'serie_id' =>  Validator::alnum("-"),
        ],
        'validatorAddSeries' => [
            'ville' => Validator::alpha("é à è ù ô î â ï ë ö ü '"),
            'map_refs' =>  Validator::intVal(),
            'dist' =>  Validator::intVal()->positive()->min(1)->max(9999),
        ],
        'validatorAddMap' => [
            'lat' =>  Validator::numeric(),
            'lng' =>  Validator::numeric(),
            'zoom' =>  Validator::intVal()->positive(),
            'ville' =>  Validator::alpha("é à è ù ô î â ï ë ö ü '"),
            'miniature' =>  Validator::stringType(),//remplacer par base64() lors de la maj de Validator/Validate
        ]
    ]
];