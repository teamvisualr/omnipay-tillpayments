<?php
/**
 * Till Gateway
 */

namespace Omnipay\TillPayments;

use Omnipay\Common\AbstractGateway;

/**
 * Till Payments Gateway
 *
 * Description here
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the Till REST Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('TillGateway');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'secretKey' => 'TEST',
 *     'testMode'  => true, // Or false when you are ready for live transactions
 * ));
 *
 * // Create a credit card object
 * // This card can be used for testing.
 * // See https://pin.net.au/docs/api/test-cards for a list of card
 * // numbers that can be used for testing.
 * $card = new CreditCard(array(
 *             'firstName'    => 'Example',
 *             'lastName'     => 'Customer',
 *             'number'       => '4200000000000000',
 *             'expiryMonth'  => '01',
 *             'expiryYear'   => '2020',
 *             'cvv'          => '123',
 *             'email'        => 'customer@example.com',
 *             'billingAddress1'       => '1 Scrubby Creek Road',
 *             'billingCountry'        => 'AU',
 *             'billingCity'           => 'Scrubby Creek',
 *             'billingPostcode'       => '4999',
 *             'billingState'          => 'QLD',
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'description'              => 'Your order for widgets',
 *     'amount'                   => '10.00',
 *     'currency'                 => 'AUD',
 *     'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *     'card'                     => $card,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 *
 * ### Test modes
 *
 * The API has two endpoint host names:
 *
 * * api.pin.net.au (live)
 * * test-api.pin.net.au (test)
 *
 * The live host is for processing live transactions, whereas the test
 * host can be used for integration testing and development.
 *
 * Each endpoint requires a different set of API keys, which can be
 * found in your account settings.
 *
 * ### Authentication
 *
 * Calls to the Till Payments API must be authenticated using HTTP
 * basic authentication, with your API key as the username, and
 * a blank string as the password.
 *
 * #### Keys
 *
 * Your account has two types of keys:
 *
 * * publishable
 * * secret
 *
 * You can find your keys on the account settings page of the dashboard
 * after you have created an account at pin.net.au and logged in.
 *
 * Your secret key can be used with all of the API, and must be kept
 * secure and secret at all times. You use your secret key from your
 * server to create charges and refunds.
 *
 * Your publishable key can be used from insecure locations (such as
 * browsers or mobile apps) to create cards with the cards API. This
 * is the key you use with Till.js to create secure payment forms in
 * the browser.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @link https://pin.net.au/docs/api
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Till';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiKey' => '',
            'username' => '',
            'password' => '',
            'testMode' => false,
            'defaultMerchantTransactionIdPrefix' => 'omnipay-',
        );
    }

    /**
     * Get API key
     *
     * Calls to the Till Payments API must be authenticated using your API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set API key
     *
     * Calls to the Till Payments API must be authenticated using your API key
     *
     * @param string $value
     * @return Gateway implements a fluent interface
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get Secret Key
     *
     * Calls to the Till Payments API must be authenticated using your Secret key
     *
     * @param string $value
     * @return Gateway implements a fluent interface
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * Set Secret Key
     *
     * Calls to the Till Payments API must be authenticated using your Secret key
     *
     * @param string $value
     * @return Gateway implements a fluent interface
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @param $value
     * @return Gateway
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param $value
     * @return Gateway
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return mixed
     */
    public function getDefaultMerchantTransactionIdPrefix()
    {
        return $this->getParameter('defaultMerchantTransactionIdPrefix');
    }

    /**
     * @param $value
     * @return Gateway
     */
    public function setDefaultMerchantTransactionIdPrefix($value)
    {
        return $this->setParameter('defaultMerchantTransactionIdPrefix', $value);
    }

    /**
     * @param string $password
     *
     * @return string
     */
    private function hashPassword($password) {
        for ($i = 0; $i < 10; $i++) {
            $password = sha1($password);
        }
        return $password;
    }

    /**
     * Create a purchase request
     * (which is called debit in their API)
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\PurchaseRequest', $parameters);
    }

    /**
     * Create an authorize request
     * (which is called preauthorize in their API)
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Create a capture request
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\CaptureRequest', $parameters);
    }

    /**
     * Create a void request
     * This voids the authorized payment
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\VoidRequest', $parameters);
    }

    /**
     * Create a refund request
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\RefundRequest', $parameters);
    }

    /**
     * Create a createCard request
     * (which is called register in their API)
     * This registers the payment instrument token
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\RegisterRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\RegisterRequest', $parameters);
    }

    /**
     * Create a deleteCard request
     * (which is called deregister in their API)
     * This removes the registered payment instrument token
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\DeregisterRequest
     */
    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\DeregisterRequest', $parameters);
    }

    /**
     * Create a payout request
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\PayoutRequest
     */
    public function payout(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\PayoutRequest', $parameters);
    }
}
