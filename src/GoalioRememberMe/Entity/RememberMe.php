<?php

namespace GoalioRememberMe\Entity;

use LmcUser\Entity\User;

class RememberMe extends User
{
    protected $sid;

    protected $token;

    public function getSid()
    {
        return $this->sid;
    }

    public function setSid($sid)
    {
        $this->sid = $sid;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setUserId($user_id)
    {
        return $this->setId($user_id);
    }

    public function getUserId()
    {
        return $this->getId();
    }
}
