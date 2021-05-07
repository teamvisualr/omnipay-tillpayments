<?php
/**
 * Till Payments Abstract Request
 */

namespace Omnipay\TillPayments\Message;

use Guzzle\Http\ClientInterface;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Helper;
use Omnipay\TillPayments\Customer;
use Omnipay\TillPayments\InvalidParameterException;
use Omnipay\TillPayments\Schedule;
use Omnipay\TillPayments\ThreeDSecureData;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Till Abstract Request
 *
 * This class forms the base class for all transaction requests
 *
 * @link https://gateway.tillpayments.com/documentation/apiv3
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const TRANSACTION_INDICATOR_SINGLE = 'SINGLE';
    const TRANSACTION_INDICATOR_INITIAL = 'INITIAL';
    const TRANSACTION_INDICATOR_RECURRING = 'RECURRING';
    const TRANSACTION_INDICATOR_CARDONFILE = 'CARDONFILE';
    const TRANSACTION_INDICATOR_CARDONFILE_MERCHANT_INITIATED = 'CARDONFILE-MERCHANT-INITIATED';
    const TRANSACTION_INDICATOR_MOTO = 'MOTO';

    /**
     * @var string
     */
    protected $liveEndpoint = 'https://gateway.tillpayments.com/api/v3/transaction/';

    /**
     * @var string
     */
    protected $testEndpoint = 'https://test-gateway.tillpayments.com/api/v3/transaction/';

    /**
     * Create a new Request
     *
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);

    }

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $parameters An associative array of parameters
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     * @throws RuntimeException
     */
    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);

        $this->setDefaultParameters();

        return $this;
    }

    /**
     * Set default parameters after initializing
     */
    public function setDefaultParameters()
    {
        if(!$this->getCustomer()) {
            $this->setCustomer(new Customer());
        }

        if(!$this->getMerchantTransactionId()) {
            $this->setMerchantTransactionId($this->getDefaultMerchantTransactionId());
        }
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
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
     * @return \Omnipay\Common\Message\AbstractRequest
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
     * @return \Omnipay\Common\Message\AbstractRequest
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
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setDefaultMerchantTransactionIdPrefix($value)
    {
        return $this->setParameter('defaultMerchantTransactionIdPrefix', $value);
    }

    /**
     * Get default merchantTransactionId
     * 
     * @return string
     */
    public function getDefaultMerchantTransactionId()
    {
        $prefix = $this->getDefaultMerchantTransactionIdPrefix();

        return uniqid($prefix);
    }

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
    public function getAdditionalId1()
    {
        return $this->getParameter('additionalId1');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setAdditionalId1($value)
    {
        return $this->setParameter('additionalId1', $value);
    }

    /**
     * @return mixed
     */
    public function getAdditionalId2()
    {
        return $this->getParameter('additionalId1');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setAdditionalId2($value)
    {
        return $this->setParameter('additionalId1', $value);
    }

    /**
     * @return mixed
     */
    public function getExtraData()
    {
        return $this->getParameter('extraData');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setExtraData($value)
    {
        return $this->setParameter('extraData', $value);
    }

    /**
     * @return mixed
     */
    public function getMerchantMetaData()
    {
        return $this->getParameter('merchantMetaData');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setMerchantMetaData($value)
    {
        return $this->setParameter('merchantMetaData', $value);
    }

    /**
     * @return mixed
     */
    public function getReferenceUuid()
    {
        return $this->getParameter('referenceUuid');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setReferenceUuid($value)
    {
        return $this->setParameter('referenceUuid', $value);
    }

    /**
     * @return mixed
     */
    public function getSuccessUrl()
    {
        return $this->getParameter('successUrl');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setSuccessUrl($value)
    {
        return $this->setParameter('successUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getCancelUrl()
    {
        return $this->getParameter('cancelUrl');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCancelUrl($value)
    {
        return $this->setParameter('cancelUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getErrorUrl()
    {
        return $this->getParameter('errorUrl');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setErrorUrl($value)
    {
        return $this->setParameter('errorUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getCallbackUrl()
    {
        return $this->getParameter('callbackUrl');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCallbackUrl($value)
    {
        return $this->setParameter('callbackUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getTransactionToken()
    {
        return $this->getParameter('transactionToken');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setTransactionToken($value)
    {
        return $this->setParameter('transactionToken', $value);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->getParameter('description');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

    /**
     * @return mixed
     */
    public function getWithRegister()
    {
        return $this->getParameter('withRegister');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setWithRegister($value)
    {
        return $this->setParameter('withRegister', $value);
    }

    /**
     * @return mixed
     */
    public function getTransactionIndicator()
    {
        return $this->getParameter('transactionIndicator');
    }

    /**
     * Possible Values:
     * SINGLE, INITIAL, RECURRING, CARDONFILE, CARDONFILE-MERCHANT-INITIATED or MOTO
     *
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setTransactionIndicator($value)
    {
        $value = strtoupper($value);
        if(!in_array($value, [
            self::TRANSACTION_INDICATOR_SINGLE,
            self::TRANSACTION_INDICATOR_INITIAL,
            self::TRANSACTION_INDICATOR_RECURRING,
            self::TRANSACTION_INDICATOR_CARDONFILE,
            self::TRANSACTION_INDICATOR_CARDONFILE_MERCHANT_INITIATED,
            self::TRANSACTION_INDICATOR_MOTO,
        ])) {
            throw new InvalidParameterException('Invalid value on transaction indicator');
        }

        return $this->setParameter('transactionIndicator', $value);
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setLanguage($value)
    {
        $value = strtolower($value);
        if(strlen($value) > 2) {
            throw new InvalidParameterException('Invalid value on language. Maximum of 2 characters.');
        }

        return $this->setParameter('language', $value);
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCustomer($value)
    {
        if ($value && !$value instanceof Customer) {
            $value = new Customer($value);
        }

        return $this->setParameter('customer', $value);
    }

    /**
     * @return mixed
     */
    public function getSchedule()
    {
        return $this->getParameter('schedule');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setSchedule($value)
    {
        if ($value && !$value instanceof Schedule) {
            $value = new Schedule($value);
        }

        return $this->setParameter('schedule', $value);
    }

    /**
     * @return mixed
     */
    public function getThreeDSecureData()
    {
        return $this->getParameter('threeDSecureData');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setThreeDSecureData($value)
    {
        if ($value && !$value instanceof ThreeDSecureData) {
            $value = new ThreeDSecureData($value);
        }

        return $this->setParameter('threeDSecureData', $value);
    }

    /**
     * This base data is always there on every transaction request payload
     *
     * @return array
     */
    protected function getBaseData()
    {
        $data = array();

        $data['merchantTransactionId'] = $this->getMerchantTransactionId();

        if($additionalId1 = $this->getAdditionalId1()) {
            $data['additionalId1'] = $additionalId1;
        }

        if($additionalId2 = $this->getAdditionalId2()) {
            $data['additionalId2'] = $additionalId2;
        }

        if($extraData = $this->getExtraData()) {
            $data['extraData'] = $extraData;
        }

        if($merchantMetaData = $this->getMerchantMetaData()) {
            $data['merchantMetaData'] = $merchantMetaData;
        }

        return $data;
    }

    /**
     * Get the customer data payload
     *
     * @return array|null
     */
    protected function getCustomerData()
    {
        $customer = $this->getCustomer();

        if($customer) {
            return $customer->getData();
        }

        return null;
    }

    /**
     * Get item data payload
     *
     * @return array
     */
    protected function getItemData()
    {
        $itemArray = array();
        $items = $this->getItems();
        if ($items) {
            foreach ($items as $item) {
                $data = array();
                $data['identification'] = strval($item->getIdentification());
                $data['name'] = strval($item->getName());
                $data['description'] = strval($item->getDescription());
                $data['quantity'] = strval($item->getQuantity());
                $data['price'] = strval($this->getPrice());
                $data['currency'] = strval($this->getCurrency());
                $data['extraData'] = strval($this->getExtraData());
                $itemArray[] = $data;
            }
        }

        return $itemArray;
    }

    /**
     * Get the schedule data payload
     *
     * @return array|null
     */
    protected function getScheduleData()
    {
        $schedule = $this->getSchedule();

        if($schedule) {
            return $schedule->getData();
        }

        return null;
    }

    /**
     * Get the customer data payload
     *
     * @return array|null
     */
    protected function getThreeDSecureDataData()
    {
        $threeDSecureDate = $this->getThreeDSecureData();

        if($threeDSecureDate) {
            return $threeDSecureDate->getData();
        }

        return null;
    }

    /**
     * Sets the card.
     * Override this method to use Customer object instead
     *
     * @param CreditCard $value
     * @return \Omnipay\Common\Message\AbstractRequest Provides a fluent interface
     */
    public function setCard($value)
    {
        if ($value) {
            if($value instanceof CreditCard) {
                $customer = new Customer();
                $customer->initializeFromCreditCard($value);
            } else {
                $customer = new Customer($value);
            }

            $this->setParameter('customer', $customer);
        }

        return $this->setParameter('card', $value);
    }

    /**
     * Sets the card reference (some kind of saved card mechanism)
     *
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest Provides a fluent interface
     */
    public function setCardReference($value)
    {
        // Till Payment uses referenceUuid of the previous transaction or the stored uui that has been "register"ed
        $this->setReferenceUuid($value);

        return $this->setParameter('cardReference', $value);
    }

    /**
     * Set the payment data of a customer
     *
     * @param mixed $value
     * @return \Omnipay\Common\Message\AbstractRequest Provides a fluent interface
     */
    public function setPaymentData($value)
    {
        return $this->getCustomer()->setPaymentData($value);
    }

    /**
     * Set the payment data of a customer
     *
     * @return PaymentData|null
     */
    public function getPaymentData()
    {
        return $this->getCustomer()->getPaymentData($value);
    }

    /**
     * Get the base endpoint + API key
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getEndpointBase() . $this->getApiKey();
    }

    /**
     * Get the base endpoint
     *
     * @return string
     */
    public function getEndpointBase()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

}
