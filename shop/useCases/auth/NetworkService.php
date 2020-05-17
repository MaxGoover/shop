<?php

namespace shop\useCases\auth;

use shop\entities\User\User;
use shop\repositories\UserRepository;

class NetworkService
{
    private $_users;

    public function __construct(UserRepository $users)
    {
        $this->_users = $users;
    }

    public function attach($id, $network, $identity): void
    {
        if ($this->_users->findByNetworkIdentity($network, $identity)) {
            throw new \DomainException('Network is already signed up.');
        }
        $user = $this->_users->get($id);
        $user->attachNetwork($network, $identity);
        $this->_users->save($user);
    }

    public function auth($network, $identity): User
    {
        if ($user = $this->_users->findByNetworkIdentity($network, $identity)) {
            return $user;
        }
        $user = User::signupByNetwork($network, $identity);
        $this->_users->save($user);
        return $user;
    }
}