<?php

namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\SignupForm;

class SignupService
{
    public function signup(SignupForm $form): User
    {
        $user = User::create(
            $form->username,
            $form->email,
            $form->password
        );

        if (!$user->save()) { // todo Потом поменять на обертку
            throw new \RuntimeException('Saving error');
        }

        return $user;
    }
}