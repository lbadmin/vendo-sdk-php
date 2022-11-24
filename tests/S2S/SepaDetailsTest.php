<?php

namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\Details\PaymentMethod\Pix;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\Vendo;

class PixDetailsTest extends \PHPUnit\Framework\TestCase
{
    public function testPixDetails()
    {
        $pix = new Pix();

        self::assertSame([
            'payment_method' => 'pix',
        ], $pix->jsonSerialize());
    }
}
