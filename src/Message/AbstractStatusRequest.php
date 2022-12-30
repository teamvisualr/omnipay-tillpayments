<?php
/**
 * Till Payments Abstract Request
 */

namespace Omnipay\TillPayments\Message;

use Omnipay\Common\CreditCard;
use Omnipay\TillPayments\Customer;

/**
 * Till Abstract Status Request
 *
 * This class forms the base class for all requests that involve pulling transaction status from till payments
 *
 * @link https://gateway.tillpayments.com/documentation/apiv3
 */
abstract class AbstractStatusRequest extends AbstractRequest
{

    /**
     * @return mixed
     */
    public function getMerchantTransactionId()
    {
        return $this->getParameter('merchantTransactionId');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setMerchantTransactionId($value)
    {
        return $this->setParameter('merchantTransactionId', $value);
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->getParameter('uuid');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setUuid($value)
    {
        return $this->setParameter('uuid', $value);
    }

    /**
     * This base data is always there on every transaction request payload
     *
     * @return array
     */
    protected function getBaseData()
    {
        $data = array();

        return $data;
    }

    /**
     * @param mixed $data
     * @return \Omnipay\Common\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function sendData($data)
    {
        $jsonBody = json_encode($data);

        // This request uses the REST endpoint and requires the JSON content type header
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), [
            'headers' => $this->buildHeaders($jsonBody),
            'body' => $jsonBody,
            'curl' => $this->getProxyConfig()
        ]);

        return $this->response = new Response($this, json_decode($httpResponse->getBody()->getContents(), true));
    }

    /**
     * Generate additional headers that are used when sending Till Payments
     *
     * @return array
     * @throws \Exception
     */
    protected function buildHeaders()
    {
        $contentType = 'application/json';

        $headers = array(
            'Content-Type' => $contentType,
            'Accept' => $contentType,
            'Authorization' => "Basic " . base64_encode($this->getUsername() . ":" . $this->getPassword()),
        );

        return $headers;
    }

    /**
     * Get the base endpoint + API key
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getEndpointBase() . 'status/' . $this->getApiKey() . '/';
    }

}
