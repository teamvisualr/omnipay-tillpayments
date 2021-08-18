<?php
/**
 * Till Payments Response
 */

namespace Omnipay\TillPayments\Message\V1;

/**
 * Till Payments Response
 *
 * This is the response class for all Till Payment requests.
 */
class TransactionStatusResponse extends Response
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
}
