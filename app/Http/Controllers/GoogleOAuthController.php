<?php

namespace App\Http\Controllers;

use App\Models\GmailAccount;
use App\Models\MailBox;
use Google_Client;
use Illuminate\Http\Request;

class GoogleOAuthController extends Controller
{
    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return response('Brak code', 400);
        }

        $client = new Google_Client();
        $client->setAuthConfig(storage_path('google/credentials.json'));

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        if (isset($token['error'])) {
            return response($token['error'], 400);
        }

        $refreshToken = $token['refresh_token'] ?? null;

        if (!$refreshToken) {
            return response('Brak refresh_token', 400);
        }

        GmailAccount::create([
            'email' => 'twoj@gmail.com',
            'refresh_token' => encrypt($refreshToken),
        ]);

        return response('OK – refresh_token zapisany:' . $refreshToken);
    }


    public function mailShow(Request $request)
    {
        $mailBox = MailBox::where('id', 1)->first();
        return response($mailBox->body);
    }
}
