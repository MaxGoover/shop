<?php

namespace shop\services\sms;

use yii\log\Logger;

class LoggedSender implements SmsSender
{
    private $_next;
    private $_logger;

    public function __construct(SmsSender $next, Logger $logger)
    {
        $this->_next = $next;
        $this->_logger = $logger;
    }

    public function send($number, $text): void
    {
        $this->_next->send($number, $text);
        $this->_logger->log('Message to ' . $number . ': ' . $text, Logger::LEVEL_INFO);
    }
}