<?php

namespace Omnipay\TillPayments\Message;

/**
 * Class TransactionStatusByMerchantTransactionIdRequest
 *
 * @package Omnipay\TillPayments\Message
 */
class TransactionStatusByMerchantTransactionIdRequest extends AbstractStatusRequest
{

    /**
     * Get data for request payload
     *
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('uuid');

        $data = $this->getBaseData();

        $data['merchantTransactionId'] = $this->getMerchantTransactionId();

        return $data;
    }

    /**
     * Get endpoint url
     *
     * @return string
     */
    public function getEndpoint()
    {
        return parent::getEndpoint() . 'getByMerchantTransactionId/' . $this->getMerchantTransactionId();
    }

}
