<?php
namespace VendoSdkUnit\Url;

use VendoSdk\Url\CustomOffer;
use VendoSdk\Vendo;

class CustomOfferTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSignedUrl()
    {
        $sharedSecret = 'test';
        $url = new CustomOffer($sharedSecret);
        $url->setSite(20);
        $url->setType('normal');
        $url->setBillingScheduleType('trial');
        $url->setInitialAmount(2.95);
        $url->setInitialDuration(3);
        $url->setRebillAmount(30);
        $url->setRebillDuration(30);
        $url->setPm(Vendo::PAYMENT_METHOD_SEPA);

        $signedUrl = $url->getSignedUrl();

        $this->assertStringContainsString('site=20', $signedUrl);
        $this->assertStringContainsString('pm=sepa', $signedUrl);
        $this->assertStringContainsString('type=normal', $signedUrl);
        $this->assertStringContainsString('billing_schedule_type=trial', $signedUrl);
        $this->assertStringContainsString('initial_amount=2.95', $signedUrl);
        $this->assertStringContainsString('signature=', $signedUrl);
        $this->assertStringContainsString('sdkv=' . Vendo::SDK_VERSION, $signedUrl);
    }

    public function testTypeInvalidThrows()
    {
        $this->expectException(\VendoSdk\Exception::class);
        $this->expectExceptionMessage('The type parameter must be normal or oneclick.');

        $url = new CustomOffer('test');
        $url->setSite(1);
        $url->setType('invalid');
    }

    public function testPmInvalidThrows()
    {
        $this->expectException(\VendoSdk\Exception::class);
        $this->expectExceptionMessage('pm must be one of:');

        $url = new CustomOffer('test');
        $url->setSite(1);
        $url->setType('normal');
        $url->setBillingScheduleType('lifetime');
        $url->setPm('invalid_pm');
    }
}
