<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => 'aptikma.co.id',
        'secret' => 'key-b74670664b112c5a0e11814dcfda5868',
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    'google' => [
        'client_id' => "659829880323-u2gcqbj5s86i62m7f9tvvq3pijcooket.apps.googleusercontent.com",
        'client_secret' => "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",
        'redirect' => 'http://aptikmamid.ngrok.io/villanesia/public/auth/google/callback',
    ],

    'firebase' => [
        'key_file_path' => env('FIREBASE_KEY_FILE_PATH'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
    ],
];
