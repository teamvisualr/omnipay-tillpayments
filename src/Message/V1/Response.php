<?php
/**
 * Till Payments Response
 */

namespace Omnipay\TillPayments\Message\V1;

use Omnipay\Common\Http\Client;
use Omnipay\Common\Http\Exception;
use Omnipay\TillPayments\Message\AbstractResponse;

/**
 * Till Payments Response
 *
 * This is the response class for all Till Payment requests.
 */
class Response extends AbstractResponse
{
    /**
     * Check if the returned response is a successful one
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $this->getCode()) === 1;
    }

    /**
     * Check if the returned response is a pending one
     *
     * @return bool
     */
    public function isPending()
    {
        return preg_match('/^(000\.200)/', $this->getCode()) === 1;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        return isset($this->data['id']) ? (string) $this->data['id'] : null;
    }

    /**
     * Get the error message from Till Payments
     *
     * @return string|null
     */
    public function getMessage()
    {
        try {
            $httpClient = new Client();
            $response = $httpClient->request('GET', 'https://test.oppwa.com/v1/resultcodes')->getBody()->getContents();
            $response = json_decode($response, true);
            foreach ($response['resultCodes'] as $item) {
                if ($item['code'] == $this->getCode()) {
                    return $item['description'];
                }
            }
        } catch (Exception $ex) {

        }
        return null;
    }

    /**
     * Get the error code from Till Payments
     *
     * @return string|null
     */
    public function getCode()
    {
        try {
            return $this->data['result']['code'];
        } catch (\Exception $ex) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return isset($this->data['redirect']);
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return isset($this->data['redirect'], $this->data['redirect']['url']) ? (string) $this->data['redirect']['url'] : null;
    }

    /**
     * Get the error message from the implemented adapter of Till Payments
     *
     * @return string|null
     */
    public function getAdapterMessage()
    {
        return $this->getMessage();
    }

    /**
     * Get the error code from the implemented adapter of Till Payments
     *
     * @return string|null
     */
    public function getAdapterCode()
    {
        return $this->getCode();
    }
}
