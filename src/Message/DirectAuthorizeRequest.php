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

        $shippingAddress = $this->getShippingAddress();

        // billing details
        $data['BillingFirstnames'] = $shippingAddress->getFirstName();
        $data['BillingSurname'] = $shippingAddress->getLastName();
        $data['BillingAddress1'] = $shippingAddress->getStreet();
        $data['BillingAddress2'] = '';
        $data['BillingCity'] = $shippingAddress->getCity();
        $data['BillingPostCode'] = $shippingAddress->getPostcode();
        $data['BillingState'] = $shippingAddress->getProvinceName();
        $data['BillingCountry'] = $shippingAddress->getCountryCode();
        $data['BillingPhone'] = $shippingAddress->getPhoneNumber();

        // shipping details
        $data['DeliveryFirstnames'] = $shippingAddress->getFirstName();
        $data['DeliverySurname'] = $shippingAddress->getLastName();
        $data['DeliveryAddress1'] = $shippingAddress->getStreet();
        $data['DeliveryAddress2'] = '';
        $data['DeliveryCity'] = $shippingAddress->getCity();
        $data['DeliveryPostCode'] = $shippingAddress->getPostcode();
        $data['DeliveryState'] = $shippingAddress->getProvinceName();
        $data['DeliveryCountry'] = $shippingAddress->getCountryCode();
        $data['DeliveryPhone'] = $shippingAddress->getPhoneNumber();

        $basketXML = $this->getItemData();
        if (!empty($basketXML)) {
            $data['BasketXML'] = $basketXML;
        }

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
