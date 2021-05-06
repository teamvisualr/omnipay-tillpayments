<?php
/**
 * Till Payments Abstract Request
 */

namespace Visualr\Omnipay\TillPayments\Message;

use Omnipay\Common\CreditCard;
use Visualr\Omnipay\TillPayments\Customer;

/**
 * Till Abstract Transaction Request
 *
 * This class forms the base class for all transaction requests
 *
 * @link https://gateway.tillpayments.com/documentation/apiv3
 */
abstract class AbstractTransactionRequest extends AbstractRequest
{

    /**
     * @param mixed $data
     * @return \Omnipay\Common\Message\ResponseInterface|RapidResponse
     */
    public function sendData($data)
    {
        $jsonBody = json_encode($data);

        // This request uses the REST endpoint and requires the JSON content type header
        $httpResponse = $this->httpClient->post($this->getEndpoint(), $this->buildHeaders($jsonBody), $jsonBody)
            ->setAuth($this->getUsername(), $this->getPassword())
            ->send();

        return $this->response = new Response($this, $httpResponse->json());
    }

    /**
     * Generate additional headers that are used when sending Till Payments
     *
     * @param $jsonBody
     * @return array
     * @throws \Exception
     */
    protected function buildHeaders($jsonBody)
    {
        $url = $this->getEndpoint();

        $timestamp = (new \DateTime('now', new \DateTimeZone('UTC')))->format('D, d M Y H:i:s T');

        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $anchor = parse_url($url, PHP_URL_FRAGMENT);

        $requestUri = $path . ($query ? '?' . $query : '') . ($anchor ? '#' . $anchor : '');

        $contentType = 'application/json';

        $hashedJsonBody = hash('sha512', $jsonBody);

        $parts = array('POST', $hashedJsonBody, $contentType, $timestamp, $requestUri);

        $str = join("\n", $parts);
        $digest = hash_hmac('sha512', $str, $this->getSecretKey(), true);
        $signature = base64_encode($digest);

        $headers = array(
            'Date' => $timestamp,
            'X-Date' => $timestamp,
            'X-Signature' => $signature,
            'Content-Type' => $contentType,
            'Accept' => $contentType,
        );

        return $headers;
    }

}
