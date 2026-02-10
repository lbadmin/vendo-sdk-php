<?php

namespace VendoSdkUnit\S2S;

use VendoSdk\S2S\Request\Details\PaymentMethod\Sepa;

class SepaDetailsTest extends \PHPUnit\Framework\TestCase
{
    public function testSepaDetailsWithIbanAndBicSwift()
    {
        $sepa = new Sepa();
        $sepa->setIban('DE1234000012340000');
        $sepa->setBicSwift('MYSWIFT000');

        self::assertSame([
            'payment_method' => 'sepa',
            'iban' => 'DE1234000012340000',
            'bic_swift' => 'MYSWIFT000',
        ], $sepa->jsonSerialize());
    }

    public function testSepaDetailsWithoutIbanOmitsIbanFromPayload()
    {
        $sepa = new Sepa();
        $sepa->setBicSwift('MYSWIFT000');

        $result = $sepa->jsonSerialize();

        self::assertArrayNotHasKey('iban', $result);
        self::assertSame('sepa', $result['payment_method']);
        self::assertSame('MYSWIFT000', $result['bic_swift']);
    }

    public function testSepaDetailsWithExplicitNullIbanOmitsIbanFromPayload()
    {
        $sepa = new Sepa();
        $sepa->setIban(null);
        $sepa->setBicSwift('MYSWIFT000');

        $result = $sepa->jsonSerialize();

        self::assertArrayNotHasKey('iban', $result);
        self::assertSame('sepa', $result['payment_method']);
    }

    public function testSepaDetailsWithNoIbanAndNoBicOmitsBothFromPayload()
    {
        $sepa = new Sepa();

        $result = $sepa->jsonSerialize();

        self::assertArrayNotHasKey('iban', $result);
        self::assertArrayNotHasKey('bic_swift', $result);
        self::assertSame('sepa', $result['payment_method']);
        self::assertSame(['payment_method' => 'sepa'], $result);
    }
}
