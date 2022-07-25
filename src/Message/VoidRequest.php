<?php

namespace Omnipay\TillPayments\Message;

/**
 * Class VoidRequest
 *
 * @package Omnipay\TillPayments\Message
 */
class VoidRequest extends AbstractTransactionRequest
{

    /**
     * Get data for request payload
     *
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('merchantTransactionId', 'referenceUuid');

        $data = $this->getBaseData();

        $data['referenceUuid'] = $this->getReferenceUuid();

        if($description = $this->getDescription()) {
            $data['description'] = $description;
        }

        if($customer = $this->getCustomerData()) {
            $data['customer'] = $customer;
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
        return parent::getEndpoint().'/void';
    }

}
