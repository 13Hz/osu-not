<?php

namespace App\Http\Controllers;

use App\Kernel\Api\OsuApi;
use App\Kernel\DTO\OauthTokenDTO;
use App\Models\OsuApiToken;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class OauthCallbackController
{
    public function handle(Request $request): View|Factory|Application
    {
        $success = false;
        if ($request->has('code')) {
            $api = new OsuApi();
            $result = $api->getToken(new OauthTokenDTO(
                client_id: config('api.client_id'),
                client_secret: config('api.client_secret'),
                code: $request->get('code'),
                redirect_uri: config('api.callback_uri')
            ));

            if ($result) {
                OsuApiToken::create([
                    'access_token' => $result->access_token,
                    'refresh_token' => $result->refresh_token,
                    'expires_in' => $result->expires_in,
                ]);
                $success = true;
            }
        }

        return view('oauth')->with('isSuccess', $success);
    }
}
