<?php

namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\SignupForm;

class SignupService
{
    public function signup(SignupForm $form): User
    {
        if (User::find()->andWhere(['username' => $form->username])) {
            throw new \DomainException('This username has already been taken.');
        }

        if (User::find()->andWhere(['email' => $form->email])) {
            throw new \DomainException('This email address has already been taken.');
        }

        $user = User::create(
            $form->username,
            $form->email,
            $form->password
        );

        // todo сюда можно также добавить sendMail()
        if (!$user->save()) { // todo Потом поменять на обертку
            throw new \RuntimeException('Saving error');
        }

        return $user;
    }
}