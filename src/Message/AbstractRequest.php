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
use Omnipay\TillPayments\Proxy;
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
     * @var null|Proxy
     */
    protected $proxy = null;

    /**
     * The request client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient = null;

    /**
     * @var string
     */
    protected $liveEndpoint = 'https://gateway.tillpayments.com/api/v3/';

    /**
     * @var string
     */
    protected $testEndpoint = 'https://test-gateway.tillpayments.com/api/v3/';

    /**
     * Endpoint for Tills PCI direct API, useful for sending credit card data directly without requiring users to use
     * payment.js tokenization or a hosted payment page
     *
     * https://gateway.tillpayments.com/documentation/json-direct-pci-enabled-api#preamble
     *
     * @var string
     */
    protected $pciDirectEndpoint = 'https://secure.tillpayments.com/api/v3/';

    public function __construct(\GuzzleHttp\ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        $this->httpClient = $httpClient;
        $this->httpRequest = $httpRequest;
        $this->initialize();
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
    protected function setDefaultParameters()
    {
        if($this->getDefaultProxy() && !$this->getProxy()) {
            $this->setProxy($this->getDefaultProxy());
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
     * Get the base endpoint
     *
     * @return string
     */
    public function getEndpointBase()
    {
        if ($this->isPciDirect()) {
            return $this->pciDirectEndpoint;
        }

        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @return mixed
     */
    public function getBearer()
    {
        return $this->getParameter('bearer');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setBearer($value)
    {
        return $this->setParameter('bearer', $value);
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->getParameter('entityId');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setEntityId($value)
    {
        return $this->setParameter('entityId', $value);
    }

    /**
     * @return mixed
     */
    public function getDefaultProxy()
    {
        return $this->getParameter('defaultProxy');
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setDefaultProxy($value)
    {
        return $this->setParameter('defaultProxy', $value);
    }

    public function setProxy(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    protected function getProxyConfig()
    {
        $proxyCurl = [];
        if ($this->getProxy()) {
            $proxyCurl[CURLOPT_PROXY] = $this->getProxy()->getUrl();
            if ($this->getProxy()->getPort()) {
                $proxyCurl[CURLOPT_PROXYPORT] = $this->getProxy()->getPort();
            }
            if ($this->getProxy()->getUsername() && $this->getProxy()->getPassword()) {
                $proxyCurl[CURLOPT_PROXYUSERPWD] = sprintf('%s:%s', $this->getProxy()->getUsername(), $this->getProxy()->getPassword());
            }

            if ($this->getProxy()->getCertUrl()) {
                $proxyCurl[CURLOPT_CAINFO] = $this->getProxy()->getCertUrl();
            }

            // Allowed for testing only
            if ($this->getProxy()->getNoVerifySSLPeer()) {
                $proxyCurl[CURLOPT_SSL_VERIFYPEER] = false;
            }
        }
        return $proxyCurl;
    }

    /**
     * If true, use Tills PCI direct API
     *
     * @return bool|null
     */
    public function isPciDirect(): ?bool
    {
        return $this->getParameter('pciDirect');
    }

    /**
     * @param bool $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setPciDirect(bool $value): \Omnipay\Common\Message\AbstractRequest
    {
        return $this->setParameter('pciDirect', $value);
    }
}
