<?php

return [
    'client_id' => env('OSU_API_CLIENT_ID'),
    'client_secret' => env('OSU_API_CLIENT_SECRET'),
    'callback_uri' => env('APP_URL') . '/oauth/callback',
    'generate_preview' => env('GENERATE_PREVIEW_IMAGE', false),
    'players_limit_enabled' => env('PLAYERS_LIMIT_ENABLED'),
    'players_check_delay' => env('OSU_CHECK_PLAYERS_SECONDS_DELAY', 10),
    'players_limit' => env('OSU_MAX_REQUESTS_PER_MINUTE') / (60 / env('OSU_CHECK_PLAYERS_SECONDS_DELAY', 10))
];
