<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    |
    | Your ScanLyser API token, generated from the API Tokens page
    | in your account settings.
    |
    */

    'token' => env('SCANLYSER_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Team ID
    |--------------------------------------------------------------------------
    |
    | The default team ID to use for API requests. This can be overridden
    | per-request by passing a team ID to resource methods.
    |
    */

    'team_id' => env('SCANLYSER_TEAM'),

];
