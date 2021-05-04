<?php
/**
 * Till Payments Abstract Response
 */

namespace Omnipay\Till\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Till Payments Abstract Response
 *
 * This is the base response class for all Till Payment requests.
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse implements RedirectResponseInterface
{

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return isset($this->data['success']) && $this->data['success'];
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        return isset($this->data['uuid']) ? (string) $this->data['uuid'] : null;
    }

    /**
     * @return string|null
     */
    public function getPurchaseReference()
    {
        return isset($this->data['purchaseId']) ? (string) $this->data['purchaseId'] : null;
    }

    /**
     * @return string|null
     */
    public function getReturnType()
    {
        return isset($this->data['returnType']) ? (string) $this->data['returnType'] : null;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return $this->getReturnType() === 'REDIRECT';
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return isset($this->data['redirectUrl']) ? (string) $this->data['redirectUrl'] : null;
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * @return array|null
     */
    public function getRedirectData()
    {
        return null;
    }

    // TODO
    public function getMessage()
    {
        $messages = array();

        if (isset($this->data['errors'])) {
            foreach ($this->data['errors'] as $error) {
                if(isset($error['adapterMessage'])) {
                    $messages[] = $error['adapterMessage'];
                } else if(isset($error['errorMessage'])) {
                    $messages[] = $error['errorMessage'];
                }
            }
        }

        return implode(', ', $messages) ?: null;
    }

    // TODO
    public function getCode()
    {
        if (isset($this->data['errors'])) {
            return $this->data['errors'];
        }
    }
}
