<?php

namespace shop\useCases\cabinet;

use shop\forms\User\ProfileEditForm;
use shop\repositories\UserRepository;

class ProfileService
{
    private $_users;

    public function __construct(UserRepository $users)
    {
        $this->_users = $users;
    }

    public function edit($id, ProfileEditForm $form): void
    {
        $user = $this->_users->get($id);
        $user->editProfile($form->email, $form->phone);
        $this->_users->save($user);
    }
}