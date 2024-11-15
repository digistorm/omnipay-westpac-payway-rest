<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 18/06/17
 * Time: 21:30
 */

namespace Omnipay\WestpacPaywayRest\Test\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;
use Omnipay\WestpacPaywayRest\Message\CreateSingleUseCardTokenRequest;

class CreateSingleUseCardTokenRequestTest extends TestCase
{
    private CreateSingleUseCardTokenRequest $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new CreateSingleUseCardTokenRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testEndpoint(): void
    {
        $this->assertSame('https://api.payway.com.au/rest/v1/single-use-tokens', $this->request->getEndpoint());
    }

    public function testGetDataInvalid(): void
    {
        $this->expectExceptionMessage('You must pass a "card" parameter.');
        $this->expectException(InvalidRequestException::class);
        $this->request->setCard(null);

        $this->request->getData();
    }

    public function testGetDataWithCard(): void
    {
        $card = $this->getValidCard();
        $this->request->setCard($card);

        $data = $this->request->getData();

        $expiryMonth = sprintf('%02d', $card['expiryMonth']);
        $name = $card['firstName'] . ' ' . $card['lastName'];

        $this->assertEquals('creditCard', $data['paymentMethod']);
        $this->assertEquals($card['number'], $data['cardNumber']);
        $this->assertEquals($name, $data['cardholderName']);
        $this->assertEquals($card['cvv'], $data['cvn']);
        $this->assertEquals($expiryMonth, $data['expiryDateMonth']);
        $this->assertEquals($card['expiryYear'], $data['expiryDateYear']);
    }
}
