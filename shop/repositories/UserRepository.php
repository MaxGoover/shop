<?php

namespace shop\repositories;

use shop\dispatchers\EventDispatcher;
use shop\entities\User\User;

class UserRepository
{
    private $_eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function findByNetworkIdentity($network, $identity): ?User
    {
        return User::find()->joinWith('networks n')->andWhere(['n.network' => $network, 'n.identity' => $identity])->one();
    }

    public function findByUsernameOrEmail($value): ?User
    {
        return User::find()->andWhere(['or', ['username' => $value], ['email' => $value]])->one();
    }

    public function get($id): User
    {
        return $this->_getBy(['id' => $id]);
    }

    /**
     * @param $productId
     * @return iterable|User[]
     */
    public function getAllByProductInWishList($productId): iterable
    {
        return User::find()
            ->alias('u')
            ->joinWith('wishlistItems w', false, 'INNER JOIN')
            ->andWhere(['w.product_id' => $productId])
            ->each();
    }

    public function getByEmail($email): User
    {
        return $this->_getBy(['email' => $email]);
    }

    public function getByEmailConfirmToken($token): User
    {
        return $this->_getBy(['email_confirm_token' => $token]);
    }

    public function getByPasswordResetToken($token): User
    {
        return $this->_getBy(['password_reset_token' => $token]);
    }

    public function existsByPasswordResetToken(string $token): bool
    {
        return (bool) User::findByPasswordResetToken($token);
    }

    public function remove(User $user): void
    {
        if (!$user->delete()) {
            throw new \RuntimeException('Removing error.');
        }
        $this->_eventDispatcher->dispatchAll($user->releaseEvents());
    }

    public function save(User $user): void
    {
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
        $this->_eventDispatcher->dispatchAll($user->releaseEvents());
    }

    private function _getBy(array $condition): User
    {
        if (!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('User not found.');
        }
        return $user;
    }
}