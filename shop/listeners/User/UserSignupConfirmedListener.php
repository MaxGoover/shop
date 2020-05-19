<?php

namespace shop\listeners\User;

use shop\entities\User\events\UserSignUpConfirmed;
use shop\services\newsletter\Newsletter;

class UserSignupConfirmedListener
{
    private $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function handle(UserSignUpConfirmed $event): void
    {
        $this->newsletter->subscribe($event->user->email);
    }
}