<?php
/**
 * Till Gateway
 */

namespace Omnipay\TillPayments;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Helper;

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
 * $gateway = Omnipay::create('TillPayments');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'apiKey' => 'your-api-key',
 *     'secretKey' => 'your-secret-key',
 *     'username' => 'your-username',
 *     'password' => 'your-password',
 *     'testMode'  => TRUE, // or FALSE when you are ready for live transactions
 *     'defaultMerchantTransactionIdPrefix'  => 'omnipay-', // prefix of the merchantTransactionId (optional)
 * ));
 *
 * // Create a credit card object
 * // This card can be used for testing.
 * // See https://pin.net.au/docs/api/test-cards for a list of card
 * // numbers that can be used for testing.
 * $card = new CreditCard(array(
 *     'firstName'       => 'Example',
 *     'lastName'        => 'Customer',
 *     'company'         => 'Visualr',
 *     'email'           => 'customer@example.com',
 *     'billingAddress1' => '1 Scrubby Creek Road',
 *     'billingCity'     => 'Scrubby Creek',
 *     'billingState'    => 'QLD',
 *     'billingPostcode' => '4999',
 *     'billingCountry'  => 'AU',
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'description'              => 'Your order for widgets',
 *     'amount'                   => '10.00',
 *     'currency'                 => 'AUD',
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
 * * https://gateway.tillpayments.com/api/v3/transaction/ (live)
 * * https://test-gateway.tillpayments.com/api/v3/transaction/ (test)
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
 * basic authentication, with your username and password.
 *
 * #### Keys
 *
 * Your account has two types of keys:
 *
 * * apiKey (different for each payment connector)
 * * secretKey
 *
 * Your apiKey will be different for each payment connector. You will
 * need to ask Till Payments for a new API key when you are integrating
 * a new payment connector.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @link https://gateway.tillpayments.com/documentation/apiv3
 */
class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'Till Payments';
    }

    public function getDefaultParameters(): array
    {
        return array(
            'apiKey' => '',
            'username' => '',
            'password' => '',
            'testMode' => false,
            'defaultProxy' => null,
            'defaultMerchantTransactionIdPrefix' => 'omnipay-',
            'pciDirect' => false
        );
    }

    /**
     * Get API key
     *
     * Calls to the Till Payments API must be authenticated using your API key
     *
     * @return string
     */
    public function getApiKey(): string
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
    public function setApiKey(string $value): Gateway
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get Secret Key
     *
     * Calls to the Till Payments API must be authenticated using your Secret key
     *
     * @return string implements a fluent interface
     */
    public function getSecretKey(): string
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
    public function setSecretKey(string $value): Gateway
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
    public function setUsername($value): Gateway
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
    public function setPassword($value): Gateway
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
    public function setDefaultMerchantTransactionIdPrefix($value): Gateway
    {
        return $this->setParameter('defaultMerchantTransactionIdPrefix', $value);
    }

    public function getDefaultProxy()
    {
        return $this->getParameter('defaultProxy');
    }

    /**
     * @param Proxy $value
     * @return Gateway
     */
    public function setDefaultProxy(Proxy $value): Gateway
    {
        return $this->setParameter('defaultProxy', $value);
    }

    /**
     * @return bool|null
     */
    public function isPciDirect(): ?bool
    {
        return $this->getParameter('pciDirect');
    }

    /**
     * @param bool $value
     * @return Gateway
     */
    public function setPciDirect(bool $value): Gateway
    {
        return $this->setParameter('pciDirect', $value);
    }

    /**
     * @param string $password
     *
     * @return string
     */
    private function hashPassword(string $password): string
    {
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
    public function purchase(array $parameters = array()): Message\PurchaseRequest
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
    public function authorize(array $parameters = array()): Message\AuthorizeRequest
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Create a capture request
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\CaptureRequest
     */
    public function capture(array $parameters = array()): Message\CaptureRequest
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
    public function void(array $parameters = array()): Message\VoidRequest
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\VoidRequest', $parameters);
    }

    /**
     * Create a refund request
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\RefundRequest
     */
    public function refund(array $parameters = array()): Message\RefundRequest
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
    public function createCard(array $parameters = array()): Message\RegisterRequest
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
    public function deleteCard(array $parameters = array()): Message\DeregisterRequest
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\DeregisterRequest', $parameters);
    }

    /**
     * Create a payout request
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\PayoutRequest
     */
    public function payout(array $parameters = array()): Message\PayoutRequest
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\PayoutRequest', $parameters);
    }

    /**
     * Get transaction status by uuid
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\TransactionStatusByUuidRequest
     */
    public function getTransactionStatusByUuid(array $parameters = array()): Message\TransactionStatusByUuidRequest
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\TransactionStatusByUuidRequest', $parameters);
    }

    /**
     * Get transaction status by merchantTransactionId
     *
     * @param array $parameters
     * @return \Omnipay\TillPayments\Message\TransactionStatusByMerchantTransactionIdRequest
     */
    public function getTransactionStatusByMerchantTransactionId(array $parameters = array()): Message\TransactionStatusByMerchantTransactionIdRequest
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\TransactionStatusByMerchantTransactionIdRequest', $parameters);
    }

    /**
     * Accept an incoming notification
     *
     * @param array $parameters
     * @return mixed
     */
    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\TillPayments\Message\NotificationRequest', $parameters);
    }

    /**
     * Get the global default HTTP client.
     *
     * @return ClientInterface
     */
    protected function getDefaultHttpClient()
    {
        return new Client();
    }
}
