<?php

declare(strict_types=1);

namespace Omnipay\WestpacPaywayRest\Test\Message;

use Omnipay\Tests\TestCase;
use Omnipay\WestpacPaywayRest\Message\PurchaseRequest;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    protected $request;

    public function setUp(): void
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'amount' => '10.00',
            'currency' => 'USD',
        ]);
    }

    public function testGetData(): void
    {
        $this->request->setCustomerNumber('ABC123');
        $this->request->setSingleUseTokenId('EFG789');

        $data = $this->request->getData();

        $this->assertEquals('payment', $data['transactionType']);
        $this->assertEquals('10.00', $data['principalAmount']);
        $this->assertEquals('usd', $data['currency']);
        $this->assertEquals('ABC123', $data['customerNumber']);
        $this->assertEquals('EFG789', $data['singleUseTokenId']);
    }
}
