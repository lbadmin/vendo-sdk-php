<?php

namespace VendoSdkUnit\S2S;

use VendoSdk\Exception;
use VendoSdk\S2S\Request\Details\PaymentMethod\GooglePay;

class GooglePayDetailsTest extends \PHPUnit\Framework\TestCase
{
    public function testGooglePayDetails()
    {
        $googlePay = new GooglePay();
        $googlePay->setToken('{"signature":"ME...","protocolVersion":"ECv2","signedMessage":"..."}');

        self::assertSame([
            'payment_method' => 'wallet',
            'provider' => 'googlepay',
            'data' => [
                'token' => '{"signature":"ME...","protocolVersion":"ECv2","signedMessage":"..."}',
            ],
        ], $googlePay->jsonSerialize());
    }

    public function testGooglePayDetailsThrowsExceptionWhenTokenIsEmpty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You must set the token field');

        $googlePay = new GooglePay();
        $googlePay->jsonSerialize();
    }

    public function testGooglePayDetailsThrowsExceptionWhenTokenIsNotSet()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You must set the token field');

        $googlePay = new GooglePay();
        $googlePay->setToken('');
        $googlePay->jsonSerialize();
    }

    public function testGooglePayGetToken()
    {
        $googlePay = new GooglePay();
        $token = '{"signature":"ME...","protocolVersion":"ECv2","signedMessage":"..."}';
        $googlePay->setToken($token);

        self::assertSame($token, $googlePay->getToken());
    }
}

