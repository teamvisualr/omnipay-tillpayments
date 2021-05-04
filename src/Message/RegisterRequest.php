<?php

namespace Omnipay\Till\Message;

/**
 * Class RegisterRequest
 *
 * @package Omnipay\Till\Message
 */
class RegisterRequest extends AbstractTransactionRequest
{

    /**
     * Get data for request payload
     *
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('merchantTransactionId');

        $data = $this->getBaseData();


        if($successUrl = $this->getSuccessUrl()) {
            $data['successUrl'] = $successUrl;
        }

        if($cancelUrl = $this->getCancelUrl()) {
            $data['cancelUrl'] = $cancelUrl;
        }

        if($errorUrl = $this->getErrorUrl()) {
            $data['errorUrl'] = $errorUrl;
        }

        if($callbackUrl = $this->getCallbackUrl()) {
            $data['callbackUrl'] = $callbackUrl;
        }


        if($transactionToken = $this->getTransactionToken()) {
            $data['transactionToken'] = $transactionToken;
        }

        if($description = $this->getDescription()) {
            $data['description'] = $description;
        }


        if($customer = $this->getCustomerData()) {
            $data['customer'] = $customer;
        }

        if($schedule = $this->getScheduleData()) {
            $data['schedule'] = $schedule;
        }

//        $data['customerProfileData'] = null; // TODO
//        $data['threeDSecureData'] = null; // TODO

        if($language = $this->getLanguage()) {
            $data['language'] = $language;
        }

        return $data;
    }

    /**
     * Get endpoint url
     *
     * @return string
     */
    public function getEndpoint()
    {
        return parent::getEndpoint().'/register';
    }

}
