<?php

namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\Details\PaymentMethod\Crypto;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\Vendo;

class CryptoDetailsTest extends \PHPUnit\Framework\TestCase
{
    public function testCryptoDetails()
    {
        $crypto = new Crypto();

        self::assertSame([
            'payment_method' => 'crypto',
        ], $crypto->jsonSerialize());
    }
}
