<?php

namespace Romichoirudin33\Sso;

use Romichoirudin33\Sso\Contracts\User;

abstract class AbstractUser implements User
{
    public $id;
    public $nickname;

    public $name;

    public $email;
    public $avatar;

    public $user;

    public function getId()
    {
        return $this->id;
    }
    public function getNickname()
    {
        return $this->nickname;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getRaw()
    {
        return $this->user;
    }

    public function setRaw(array $user)
    {
        $this->user = $user;

        return $this;
    }

    public function map(array $attributes)
    {
        $this->attributes = $attributes;

        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }
}
