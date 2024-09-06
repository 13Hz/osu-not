<?php

namespace App\Http\Controllers;

use App\Kernel\Api\OsuApi;
use App\Kernel\DTO\OauthTokenDTO;
use Illuminate\Http\Request;

class OauthCallbackController extends Controller
{
    public function handle(Request $request): void
    {
        if ($request->has('code')) {
            $api = new OsuApi();
            $result = $api->getToken(new OauthTokenDTO(
                client_id: config('api.client_id'),
                client_secret: config('api.client_secret'),
                code: $request->get('code'),
                redirect_uri: config('api.callback_uri')
            ));

            dd($result);
        }
    }
}
