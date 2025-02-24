<?php

declare(strict_types=1);

namespace Omnipay\WestpacPaywayRest\Message;

use Money\Money;
use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

/**
 * @link https://www.payway.com.au/rest-docs/index.html
 */
abstract class AbstractRequest extends CommonAbstractRequest
{
    protected const ENDPOINT = 'https://api.payway.com.au/rest/v1';

    abstract public function getEndpoint(): string;

    /**
     * Get API publishable key
     */
    public function getApiKeyPublic(): ?string
    {
        return $this->getParameter('apiKeyPublic');
    }

    /**
     * Set API publishable key
     */
    public function setApiKeyPublic(string $value): self
    {
        return $this->setParameter('apiKeyPublic', $value);
    }

    /**
     * Get API secret key
     */
    public function getApiKeySecret(): ?string
    {
        return $this->getParameter('apiKeySecret');
    }

    /**
     * Set API secret key
     */
    public function setApiKeySecret(string $value): self
    {
        return $this->setParameter('apiKeySecret', $value);
    }

    /**
     * Get Merchant
     */
    public function getMerchantId(): ?string
    {
        return $this->getParameter('merchantId');
    }

    /**
     * Set Merchant
     */
    public function setMerchantId(string $value): self
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * Get Use Secret Key setting
     */
    public function getUseSecretKey(): ?bool
    {
        return (bool) $this->getParameter('useSecretKey');
    }

    /**
     * Set Use Secret Key setting
     */
    public function setUseSecretKey(string|bool $value): self
    {
        return $this->setParameter('useSecretKey', (bool) $value);
    }

    /**
     * Get single-use token
     */
    public function getSingleUseTokenId(): ?string
    {
        return $this->getParameter('singleUseTokenId');
    }

    /**
     * Set single-use token
     */
    public function setSingleUseTokenId(string $value): self
    {
        return $this->setParameter('singleUseTokenId', $value);
    }

    /**
     * Get Idempotency Key
     */
    public function getIdempotencyKey(): ?string
    {
        return $this->getParameter('idempotencyKey') ?: uniqid();
    }

    /**
     * Set Idempotency Key
     */
    public function setIdempotencyKey(string $value): self
    {
        return $this->setParameter('idempotencyKey', $value);
    }

    public function getCustomerNumber(): ?string
    {
        return $this->getParameter('customerNumber');
    }

    public function setCustomerNumber(string $value): self
    {
        return $this->setParameter('customerNumber', $value);
    }

    public function getTransactionType(): ?string
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType(string $value): self
    {
        return $this->setParameter('transactionType', $value);
    }

    public function getAmount(): string|Money|null
    {
        return $this->getParameter('amount');
    }

    /**
     * Retaining the original method signature, although we use the method incorrectly by allowing Money param
     * @see \Omnipay\Common\Message\AbstractRequest::setAmount
     * @param string|null|Money $value
     * @return $this
     */
    public function setAmount($value): self
    {
        return $this->setParameter('amount', $value);
    }

    public function getPrincipalAmount(): ?string
    {
        return $this->getParameter('principalAmount');
    }

    public function setPrincipalAmount(string $value): self
    {
        return $this->setParameter('principalAmount', $value);
    }

    public function getCurrency(): ?string
    {
        // PayWay expects lowercase currency values
        return ($this->getParameter('currency'))
            ? strtolower((string) $this->getParameter('currency'))
            : null;
    }

    /**
     * Retaining the original method signature
     *
     * @param string $value
     * @return $this
     */
    public function setCurrency($value): self
    {
        return $this->setParameter('currency', $value);
    }

    public function getOrderNumber(): ?string
    {
        return $this->getParameter('orderNumber');
    }

    public function setOrderNumber(string $value): self
    {
        return $this->setParameter('orderNumber', $value);
    }

    public function getCustomerIpAddress(): ?string
    {
        return $this->getParameter('customerIpAddress');
    }

    public function setCustomerIpAddress(string $value): self
    {
        return $this->setParameter('customerIpAddress', $value);
    }

    /**
     * Get HTTP method
     */
    public function getHttpMethod(): string
    {
        return 'GET';
    }

    /**
     * Get request headers
     */
    public function getRequestHeaders(): array
    {
        // common headers
        $headers = ['Accept' => 'application/json'];

        // set content type
        if ($this->getHttpMethod() !== 'GET') {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        // prevent duplicate POSTs
        if ($this->getHttpMethod() === 'POST') {
            $headers['Idempotency-Key'] = $this->getIdempotencyKey();
        }

        return $headers;
    }

    /**
     * Send data request
     */
    public function sendData(mixed $data): ResponseInterface
    {
        // get the appropriate API key
        $apiKey = ($this->getUseSecretKey()) ? $this->getApiKeySecret() : $this->getApiKeyPublic();

        $headers = $this->getRequestHeaders();
        $headers['Authorization'] = 'Basic ' . base64_encode($apiKey . ':');

        $body = $data ? http_build_query($data, '', '&') : null;

        $response = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $headers,
            $body,
        );

        $this->response = new Response($this, json_decode($response->getBody()->getContents(), true));

        // save additional info
        $this->response->setHttpResponseCode($response->getStatusCode());
        $this->response->setTransactionType($this->getTransactionType());

        return $this->response;
    }
}
