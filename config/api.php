<?php

return [
    'client_id' => env('OSU_API_CLIENT_ID'),
    'client_secret' => env('OSU_API_CLIENT_SECRET'),
    'callback_uri' => env('APP_URL') . '/oauth/callback'
];
