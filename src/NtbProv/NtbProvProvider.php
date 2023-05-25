<?php

namespace Romichoirudin33\Sso\NtbProv;

use GuzzleHttp\RequestOptions;

class NtbProvProvider extends AbstractProvider implements ProviderInterface
{

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('http://sso.test/oauth/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return 'http://sso.test/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('http://sso.test/api/user', [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserWithToken(array $user, array $token)
    {
        $user['token'] = $token;
        return $user;
    }
}
