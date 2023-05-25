<?php

namespace Romichoirudin33\Sso\NtbProv;

use Romichoirudin33\Sso\AbstractUser;

class User extends AbstractUser
{

    public $token;

    public $refreshToken;

    public $expiresIn;


    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

}
