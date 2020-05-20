<?php

namespace shop\listeners\Shop\Product;

use shop\entities\Shop\Product\events\ProductAppearedInStock;
use shop\entities\Shop\Product\Product;
use shop\entities\User\User;
use shop\repositories\UserRepository;
use yii\base\ErrorHandler;
use yii\mail\MailerInterface;

class ProductAppearedInStockListener
{
    private $_userRepository;
    private $_mailer;
    private $_errorHandler;

    public function __construct(
        UserRepository $userRepository,
        MailerInterface $mailer,
        ErrorHandler $errorHandler)
    {
        $this->_userRepository = $userRepository;
        $this->_mailer = $mailer;
        $this->_errorHandler = $errorHandler;
    }

    public function handle(ProductAppearedInStock $event): void
    {
        if ($event->product->isActive()) {
            foreach ($this->_userRepository->getAllByProductInWishList($event->product->id) as $user) {
                if ($user->isActive()) {
                    try {
                        $this->_sendEmailNotification($user, $event->product);
                    } catch (\Exception $e) {
                        $this->_errorHandler->handleException($e);
                    }
                }
            }
        }
    }

    private function _sendEmailNotification(User $user, Product $product): void
    {
        $sent = $this->_mailer
            ->compose(
                ['html' => 'shop/wishlist/available-html', 'text' => 'shop/wishlist/available-text'],
                ['user' => $user, 'product' => $product]
            )
            ->setTo($user->email)
            ->setSubject('Product is available')
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Email sending error to ' . $user->email);
        }
    }
}