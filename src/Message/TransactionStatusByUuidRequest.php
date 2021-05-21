<?php

namespace Omnipay\TillPayments\Message;

/**
 * Class TransactionStatusByUuidRequest
 *
 * @package Omnipay\TillPayments\Message
 */
class TransactionStatusByUuidRequest extends AbstractStatusRequest
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

        $data['uuid'] = $this->getUuid();

        return $data;
    }

    /**
     * Get endpoint url
     *
     * @return string
     */
    public function getEndpoint()
    {
        return parent::getEndpoint() . 'getByUuid/' . $this->getUuid();
    }

}
