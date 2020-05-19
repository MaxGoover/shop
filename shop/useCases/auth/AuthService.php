<?php

namespace shop\useCases\auth;

use shop\entities\User\User;
use shop\forms\auth\LoginForm;
use shop\repositories\UserRepository;

class AuthService
{
    private $_users;

    public function __construct(UserRepository $users)
    {
        $this->_users = $users;
    }

    public function auth(LoginForm $form): User
    {
        $user = $this->_users->findByUsernameOrEmail($form->username);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new \DomainException('Undefined user or password.');
        }
        return $user;
    }
}