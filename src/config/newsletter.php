<?php

return [


    'driver' => env('MAILCHIMP_DRIVER', 'api'),

    'apiKey' => env('MAILCHIMP_APIKEY'),

    'defaultListName' => 'subscribers',

    'lists' => [

        'subscribers' => [

            'id' => env('MAILCHIMP_LIST_ID'),
        ],
    ],

    'ssl' => true,

];
