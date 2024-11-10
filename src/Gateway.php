<?php

declare(strict_types=1);

namespace Omnipay\WestpacPaywayRest;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\WestpacPaywayRest\Message\CreateSingleUseCardTokenRequest;
use Omnipay\WestpacPaywayRest\Message\PurchaseRequest;

/**
 * @method RequestInterface authorize(array $options = []) (Optional method)
 * Authorize an amount on the customers card
 * @method RequestInterface completeAuthorize(array $options = []) (Optional method)
 * Handle return from off-site gateways after authorization
 * @method RequestInterface capture(array $options = []) (Optional method)
 * Capture an amount you have previously authorized
 * @method RequestInterface completePurchase(array $options = []) (Optional method)
 * Handle return from off-site gateways after purchase
 * @method RequestInterface refund(array $options = []) (Optional method)
 * Refund an already processed transaction
 * @method RequestInterface void(array $options = []) (Optional method)
 * Generally can only be called up to 24 hours after submitting a transaction
 * @method RequestInterface createCard(array $options = []) (Optional method)
 * The returned response object includes a cardReference, which can be used for future transactions
 * @method RequestInterface updateCard(array $options = []) (Optional method)
 * Update a stored card
 * @method RequestInterface deleteCard(array $options = []) (Optional method)
 * Delete a stored card
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     * @return string
     */
    public function getName(): string
    {
        return 'Westpac PayWay REST API';
    }

    /**
     * Get gateway short name
     *
     * This name can be used with GatewayFactory as an alias of the gateway class,
     * to create new instances of this gateway.
     * @return string
     */
    public function getShortName(): string
    {
        return 'PayWay';
    }

    public function getDefaultParameters(): array
    {
        return ['apiKeyPublic' => '', 'apiKeySecret' => '', 'merchantId' => ''];
    }

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

    public function getUseSecretKey(): bool
    {
        return false;
    }

    /**
     * Purchase request
     */
    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * Create singleUseTokenId with a CreditCard
     */
    public function createSingleUseCardToken(array $options = []): AbstractRequest
    {
        return $this->createRequest(CreateSingleUseCardTokenRequest::class, $options);
    }
}
