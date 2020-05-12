<?php

namespace common\repositories;

use common\entities\User;

class UserRepository
{
    public function existsByPasswordResetToken(string $token): bool
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    public function findByUsernameOrEmail(string $value): ?User
    {
        return User::find()->andWhere(['or', ['username' => $value], ['email' => $value]])->one();
    }

    public function getByEmail($email): User
    {
        return $this->_getBy(['email' => $email]);
    }

    public function getByEmailConfirmToken(string $token): User
    {
        return $this->_getBy(['email_confirm_token' => $token]);
    }

    public function getByPasswordResetToken(string $token): User
    {
        return $this->_getBy(['password_reset_token' => $token]);
    }

    public function save(User $user): void
    {
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    private function _getBy(array $condition): User
    {
        if (!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('User not found.');
        }
        return $user;
    }
}