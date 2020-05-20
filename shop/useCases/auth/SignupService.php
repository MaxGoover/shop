<?php

namespace shop\useCases\auth;

use shop\access\Rbac;
use shop\entities\User\User;
use shop\forms\auth\SignupForm;
use shop\repositories\UserRepository;
use shop\services\RoleManager;
use shop\services\TransactionManager;

class SignupService
{
    private $_users;
    private $_roles;
    private $_transaction;

    public function __construct(
        UserRepository $users,
        RoleManager $roles,
        TransactionManager $transaction
    )
    {
        $this->_users = $users;
        $this->_roles = $roles;
        $this->_transaction = $transaction;
    }

    public function confirm($token): void
    {
        if (empty($token)) {
            throw new \DomainException('Empty confirm token.');
        }
        $user = $this->_users->getByEmailConfirmToken($token);
        $user->confirmSignup();
        $this->_users->save($user);
    }

    public function signup(SignupForm $form): void
    {
        $user = User::requestSignup(
            $form->username,
            $form->email,
            $form->phone,
            $form->password
        );
        $this->_transaction->wrap(function () use ($user) {
            $this->_users->save($user);
            $this->_roles->assign($user->id, Rbac::ROLE_USER);
        });
    }
}