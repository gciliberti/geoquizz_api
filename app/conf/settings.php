<?php
use Respect\Validation\Validator;

return [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => '../conf/config.ini',
        'validatorAddCommand' => [
            'nom' => Validator::alpha("é à è ù ô î â ï ë ö"),
            'mail' =>  Validator::email(),
            'livraison' => [
                'date' => Validator::date('d-m-Y')->min('now'),
                'heure' => Validator::date('H:i:s')
            ],
            'client_id' => Validator::optional(Validator::intVal()),
            'items' => [
                //'uri' => Validator::alnum('/'),
                //'q' => Validator::numeric()
            ]
        ]
    ]
];