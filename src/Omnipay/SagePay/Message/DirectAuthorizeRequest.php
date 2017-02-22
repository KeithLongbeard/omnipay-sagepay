<?php

namespace Omnipay\SagePay\Message;

/**
 * Sage Pay Direct Authorize Request
 */
class DirectAuthorizeRequest extends AbstractRequest
{
    protected $action = 'DEFERRED';
    protected $cardBrandMap = array(
        'mastercard' => 'mc',
        'diners_club' => 'dc'
    );

    protected function getBaseAuthorizeData()
    {
        //$this->validate('amount', 'card', 'transactionId');
        $card = $this->getCard();

        $data = $this->getBaseData();
        $data['Description'] = $this->getDescription();
        $data['Amount'] = $this->getAmount();
        $data['Currency'] = $this->getCurrency();
        $data['VendorTxCode'] = rand(10000,999999);
        $data['ClientIPAddress'] = $this->getClientIp();
        $data['ApplyAVSCV2'] = 0;
        $data['Apply3DSecure'] = 0;
        $data['NotificationURL'] = $this->getNotifyUrl();


        $data['ReferrerID'] = 'Main';


        // billing details
        $data['BillingFirstnames'] = 'Nolan';
        $data['BillingSurname'] = 'Cain';
        $data['BillingAddress1'] = '36 Baker Street';
        $data['BillingAddress2'] = '';
        $data['BillingCity'] = 'London';
        $data['BillingPostCode'] = 'WC1H 8EA';
        $data['BillingState'] = '';
        $data['BillingCountry'] = 'GB';
        $data['BillingPhone'] = '';

        // shipping details
        $data['DeliveryFirstnames'] = 'Nolan';
        $data['DeliverySurname'] = 'Cain';
        $data['DeliveryAddress1'] = '36 Baker Street';
        $data['DeliveryAddress2'] = '';
        $data['DeliveryCity'] = 'London';
        $data['DeliveryPostCode'] = 'WC1H 8EA';
        $data['DeliveryState'] = '';
        $data['DeliveryCountry'] = 'GB';
        $data['DeliveryPhone'] = '';
        $data['CustomerEMail'] = 'nono@theodo.co.uk';

        return $data;
    }

    public function getData()
    {
        $data = $this->getBaseAuthorizeData();
        $this->getCard()->validate();

        $data['CardHolder'] = $this->getCard()->getName();
        $data['CardNumber'] = $this->getCard()->getNumber();
        $data['CV2'] = $this->getCard()->getCvv();
        $data['ExpiryDate'] = $this->getCard()->getExpiryDate('my');
        $data['CardType'] = $this->getCardBrand();

        if ($this->getCard()->getStartMonth() and $this->getCard()->getStartYear()) {
            $data['StartDate'] = $this->getCard()->getStartDate('my');
        }

        if ($this->getCard()->getIssueNumber()) {
            $data['IssueNumber'] = $this->getCard()->getIssueNumber();
        }

        return $data;
    }

    public function getService()
    {
        return 'vspdirect-register';
    }

    protected function getCardBrand()
    {
        $brand = $this->getCard()->getBrand();

        if (isset($this->cardBrandMap[$brand])) {
            return $this->cardBrandMap[$brand];
        }

        return $brand;
    }
}
