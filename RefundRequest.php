<?php

namespace Omnipay\TillPayments\Message;

/**
 * Class RefundRequest
 *
 * @package Omnipay\TillPayments\Message
 */
class RefundRequest extends AbstractTransactionRequest
{

    /**
     * Get data for request payload
     *
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('merchantTransactionId', 'amount', 'currency', 'referenceUuid');

        $data = $this->getBaseData();

        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['referenceUuid'] = $this->getReferenceUuid();

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

        if($items = $this->getItemData()) {
            $data['items'] = $items;
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
        return parent::getEndpoint().'/refund';
    }

}
