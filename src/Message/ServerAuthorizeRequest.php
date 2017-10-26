<?php

namespace Omnipay\SagePay\Message;

/**
 * Sage Pay Server Authorize Request
 */
class ServerAuthorizeRequest extends DirectAuthorizeRequest
{
    public function getProfile()
    {
        return "LOW"; //$this->getParameter('profile');
    }

    public function setProfile($value)
    {
        return $this->setParameter('profile', $value);
    }

    public function getData()
    {
        $this->validate('notifyUrl');

        $data = $this->getBaseAuthorizeData();
        $httpHost = $_SERVER['HTTP_HOST'];
        $httpPrefix = 'http'; //(substr($httpHost, -strlen('com')) === 'com') ? 'https' : 'http';
        $notifyUrl = $this->getNotifyUrl();
        $customNotifyUrl = str_replace('http', $httpPrefix, $notifyUrl);
        $data['NotificationURL'] =  $customNotifyUrl;
        $data['Profile'] = $this->getProfile();

        return $data;
    }

    public function getService()
    {
        return 'vspserver-register';
    }

    protected function createResponse($data)
    {
        return $this->response = new ServerAuthorizeResponse($this, $data);
    }
}
