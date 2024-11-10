<?php

declare(strict_types=1);

namespace Omnipay\WestpacPaywayRest\Message;

use Omnipay\Common\Message\AbstractResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Response class for all WestpacPaywayRest requests
 */
class Response extends AbstractResponse
{
    protected string $requestId;

    protected string $httpResponseCode;

    protected string $transactionType;

    /**
     * Is the transaction successful?
     * @return bool True if successful
     */
    public function isSuccessful(): bool
    {
        // get response code
        $code = $this->getHttpResponseCode();

        if ($code === '200') {  // OK
            return true;
        }

        if ($code === '201') {   // Created
            if ($this->getTransactionType() === 'payment') {
                return $this->isApproved();
            }

            return true;
        }

        // Accepted
        return $code === '202' && $this->isPending();
    }

    /**
     * Is the transaction approved?
     */
    public function isApproved(): bool
    {
        return in_array($this->getStatus(), ['approved', 'approved*']);
    }

    /**
     * Is the transaction pending?
     */
    public function isPending(): bool
    {
        return $this->getTransactionType() === 'payment' && $this->getStatus() === 'pending';
    }

    /**
     * Get Transaction ID
     */
    public function getTransactionId(): ?string
    {
        return (string) $this->getDataItem('transactionId');
    }

    /**
     * Get Transaction reference
     */
    public function getTransactionReference(): ?string
    {
        return (string) $this->getDataItem('receiptNumber');
    }

    /**
     * Get Customer Number
     */
    public function getCustomerNumber(): ?string
    {
        return (string) $this->getDataItem('customerNumber');
    }

    /**
     * Get Contact details
     */
    public function getContact(): array
    {
        return $this->getDataItemArray('contact');
    }

    /**
     * Get status
     */
    public function getStatus(): string
    {
        return (string) $this->getDataItem('status');
    }

    public function getDataItemArray(string $key): ?array
    {
        $item = $this->getData()[$key] ?? null;
        if (!(is_array($item) || is_null($item))) {
            throw new \InvalidArgumentException("Data item $key is not an array");
        }

        return $item;
    }

    public function getDataItem(string $key): bool|string|int|float|null
    {
        $item = $this->getData()[$key] ?? null;
        if (!(is_scalar($item) || is_null($item))) {
            throw new \InvalidArgumentException("Data item $key is not a scalar value");
        }

        return $item;
    }

    /**
     * Get response data, optionally by key
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getErrorDataItem(string $key): bool|string|int|float|null
    {
        $data = $this->getErrorData();
        if (!(is_scalar($data[$key]) || is_null($data[$key]))) {
            throw new \InvalidArgumentException("Error data item $key is not a scalar value");
        }

        return $data[$key] ?? null;
    }

    /**
     * Get error data from response
     */
    public function getErrorData(): ?array
    {
        if ($this->isSuccessful()) {
            return null;
        }

        // get error data (array in data)
        return $this->getDataItemArray('data')[0] ?? null;
    }

    /**
     * Get error message from the response
     */
    public function getMessage(): ?string
    {
        if ($this->getErrorMessage()) {
            return $this->getErrorMessage() . ' (' . $this->getErrorFieldName() . ')';
        }

        if ($this->isSuccessful()) {
            return ($this->getStatus()) ? ucfirst($this->getStatus()) : 'Successful';
        }

        // default to unsuccessful message
        return 'The transaction was unsuccessful.';
    }

    /**
     * Get code
     */
    public function getCode(): ?string
    {
        return implode(' ', [
            $this->getResponseCode(),
            $this->getResponseText(),
            '(' . $this->getHttpResponseCode(),
            $this->getHttpResponseCodeText() . ')',
        ]);
    }

    /**
     * Get error message from the response
     */
    public function getErrorMessage(): ?string
    {
        return (string) $this->getErrorDataItem('message');
    }

    /**
     * Get field name in error from the response
     */
    public function getErrorFieldName(): ?string
    {
        return (string) $this->getErrorDataItem('fieldName');
    }

    /**
     * Get field value in error from the response
     */
    public function getErrorFieldValue(): ?string
    {
        return (string) $this->getErrorDataItem('fieldValue');
    }

    /**
     * Get Payway Response Code
     */
    public function getResponseCode(): string
    {
        return (string) $this->getDataItem('responseCode');
    }

    /**
     * Get Payway Response Text
     */
    public function getResponseText(): string
    {
        return (string) $this->getDataItem('responseText');
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * Set request id
     */
    public function setRequestId(string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get HTTP Response Code
     */
    public function getHttpResponseCode(): string
    {
        return $this->httpResponseCode;
    }

    /**
     * Set HTTP Response Code
     */
    public function setHttpResponseCode(int $value): self
    {
        $this->httpResponseCode = (string) $value;

        return $this;
    }

    /**
     * Get HTTP Response code text
     */
    public function getHttpResponseCodeText(): ?string
    {
        $code = $this->getHttpResponseCode();
        $statusTexts = SymfonyResponse::$statusTexts;

        return $statusTexts[$code] ?? null;
    }

    /**
     * Get transaction type
     */
    public function getTransactionType(): ?string
    {
        return (string) $this->getDataItem('transactionType');
    }

    /**
     * Get payment method
     */
    public function getPaymentMethod(): ?string
    {
        return (string) $this->getDataItem('paymentMethod');
    }

    /**
     * Get credit card information
     */
    public function getCreditCard(): ?string
    {
        return (string) $this->getDataItem('creditCard');
    }

    /**
     * Get bank account information
     */
    public function getBankAccount(): ?string
    {
        return (string) $this->getDataItem('bankAccount');
    }

    /**
     * Set Transaction Type
     */
    public function setTransactionType(string $value): self
    {
        $this->transactionType = $value;

        return $this;
    }
}
