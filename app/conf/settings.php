<?php
use Respect\Validation\Validator;

return [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => '../conf/config.ini',
        'validatorRegister' => [
            'nom' => Validator::alpha("é à è ù ô î â ï ë ö"),
            'prenom' =>  Validator::alpha("é à è ù ô î â ï ë ö"),
            'mail' =>  Validator::email(),
            'motdepasse' =>  Validator::stringType()->length(4, null),
            'telephone' =>  Validator::phone(),
            //'client_id' => Validator::optional(Validator::intVal()),
        ]
    ]
];