<?php

namespace shop\listeners\User;

use shop\entities\User\events\UserSignUpConfirmed;
use shop\services\newsletter\Newsletter;

class UserSignupConfirmedListener
{
    private $_newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->_newsletter = $newsletter;
    }

    public function handle(UserSignUpConfirmed $event): void
    {
        $this->_newsletter->subscribe($event->user->email);
    }
}