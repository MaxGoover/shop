<?php

namespace shop\services\newsletter;

class MailChimp implements Newsletter
{
    private $_client;
    private $_listId;

    public function __construct(\DrewM\MailChimp\MailChimp $client, $listId)
    {
        $this->_client = $client;
        $this->_listId = $listId;
    }

    public function subscribe($email): void
    {
        $this->_client->post('lists/' . $this->_listId . '/members', [
            'email_address' => $email,
            'status' => 'subscribed',
        ]);
        if ($error = $this->_client->getLastError()) {
            throw new \RuntimeException($error);
        }
    }

    public function unsubscribe($email): void
    {
        $hash = $this->_client->subscriberHash($email);
        $this->_client->delete('lists/' . $this->_listId . '/members/' . $hash);
        if ($error = $this->client->getLastError()) {
            throw new \RuntimeException($error);
        }
    }
}