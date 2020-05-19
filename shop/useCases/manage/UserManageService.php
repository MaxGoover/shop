<?php

namespace shop\useCases\manage;

use shop\entities\User\User;
use shop\forms\manage\User\UserCreateForm;
use shop\forms\manage\User\UserEditForm;
use shop\repositories\UserRepository;
use shop\services\newsletter\Newsletter;
use shop\services\RoleManager;
use shop\services\TransactionManager;

class UserManageService
{
    private $_repository;
    private $_roles;
    private $_transaction;
    /**
     * @var Newsletter
     */
    private $_newsletter;

    public function __construct(
        UserRepository $repository,
        RoleManager $roles,
        TransactionManager $transaction,
        Newsletter $newsletter
    )
    {
        $this->_repository = $repository;
        $this->_roles = $roles;
        $this->_transaction = $transaction;
        $this->_newsletter = $newsletter;
    }

    public function assignRole($id, $role): void
    {
        $user = $this->_repository->get($id);
        $this->_roles->assign($user->id, $role);
    }

    public function create(UserCreateForm $form): User
    {
        $user = User::create(
            $form->username,
            $form->email,
            $form->phone,
            $form->password
        );
        $this->_transaction->wrap(function () use ($user, $form) {
            $this->_repository->save($user);
            $this->_roles->assign($user->id, $form->role);
            $this->_newsletter->subscribe($user->email);
        });
        return $user;
    }

    public function edit($id, UserEditForm $form): void
    {
        $user = $this->_repository->get($id);
        $user->edit(
            $form->username,
            $form->email,
            $form->phone
        );
        $this->_transaction->wrap(function () use ($user, $form) {
            $this->_repository->save($user);
            $this->_roles->assign($user->id, $form->role);
        });
    }

    public function remove($id): void
    {
        $user = $this->_repository->get($id);
        $this->_repository->remove($user);
        $this->_newsletter->unsubscribe($user->email);
    }
}