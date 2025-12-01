<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase service account JSON file
    |
    */
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL (Optional)
    |--------------------------------------------------------------------------
    */
    'database_url' => env('FIREBASE_DATABASE_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    */
    'project_id' => env('FIREBASE_PROJECT_ID', ''),
];
