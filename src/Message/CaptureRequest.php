<?php

namespace Omnipay\TillPayments\Message;

/**
 * Class CaptureRequest
 *
 * @package Omnipay\TillPayments\Message
 */
class CaptureRequest extends AbstractTransactionRequest
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

        if($referenceUuid = $this->getReferenceUuid()) {
            $data['referenceUuid'] = $referenceUuid;
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
        return parent::getEndpoint().'/capture';
    }

}
