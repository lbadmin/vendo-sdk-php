<?php

namespace VendoSdkUnit\Gateway;

use VendoSdk\Gateway\TokenPayment;
use VendoSdk\Gateway\Request\Details\Token;

class TokenPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testApiEndpoint()
    {
        $payment = new TokenPayment();
        self::assertEquals('https://secure.vend-o.com/api/gateway/payment', $payment->getApiEndpoint());
    }

    public function testSetTokenDetails()
    {
        $payment = $this->createPartialMock(TokenPayment::class, [
            'getBaseFields'
        ]);

        $payment->method('getBaseFields')->willReturn([
            'base key1' => 'base value 1',
        ]);

        $payment->setSiteId(12345);

        $tokenDetails = new Token();
        $tokenDetails->setToken('test-payment-token');
        $payment->setPaymentDetailsToken($tokenDetails);
        self::assertEquals($tokenDetails, $payment->getPaymentDetailsToken());
        self::assertEquals([
            'base key1' => 'base value 1',
            'site_id' => 12345,
            'preauth_only' => false,
            'payment_details' => $tokenDetails,
        ], $payment->jsonSerialize());
    }
}
