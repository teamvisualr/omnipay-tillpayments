<?php
namespace Omnipay\TillPayments;

use Omnipay\Common\Helper;
use Omnipay\Common\ParametersTrait;
use Omnipay\TillPayments\Traits\ParameterBagTrait;

class Proxy {
    use ParameterBagTrait;

    public function __construct(array $options = [])
    {
        $this->initialize($options);
    }

    public function setEnabled(bool $value): Proxy
    {
        return $this->setParameter('enabled', $value);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->getParameter('enabled');
    }

    public function setUrl(string $value = null): Proxy
    {
        return $this->setParameter('url', $value);
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->getParameter('url');
    }

    public function setPort(string $value = null): Proxy
    {
        return $this->setParameter('port', $value);
    }

    /**
     * @return null|string
     */
    public function getPort()
    {
        return $this->getParameter('port');
    }

    public function setUsername(string $value = null): Proxy
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setPassword(string $value = null): Proxy
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setCertUrl(string $value = null): Proxy
    {
        return $this->setParameter('certificateUrl', $value);
    }

    /**
     * @return null|string
     */
    public function getCertUrl()
    {
        return $this->getParameter('certificateUrl');
    }

    public function setNoVerifySSLPeer(bool $value = null): Proxy
    {
        return $this->setParameter('noVerifySSLPeer', $value);
    }

    public function getNoVerifySSLPeer(): bool
    {
        return (bool) $this->getParameter('noVerifySSLPeer');
    }
}