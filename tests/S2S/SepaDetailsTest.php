<?php

namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\Details\PaymentMethod\Pix;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\PaymentMethod\Sepa;
use VendoSdk\Vendo;

class SepaDetailsTest extends \PHPUnit\Framework\TestCase
{
    public function testSepaDetails()
    {
        $pix = new Sepa();
        $pix->setIban('DE1234000012340000');
        $pix->setBicSwift('MYSWIFT000');

        self::assertSame([
            'payment_method' => 'sepa',
            'iban' => 'DE1234000012340000',
            'bic_swift' => 'MYSWIFT000',
        ], $pix->jsonSerialize());
    }
}
