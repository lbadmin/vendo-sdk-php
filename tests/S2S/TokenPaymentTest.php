<?php

namespace VendoSdkUnit\S2S;

use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\Customer;
use VendoSdk\S2S\Request\Details\ExternalReferences;
use VendoSdk\S2S\Request\Details\ShippingAddress;
use VendoSdk\S2S\Request\Payment;
use VendoSdk\S2S\Request\Details\PaymentMethod\Token;

class TokenPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testSetTokenDetails()
    {
        $payment = $this->createPartialMock(Payment::class, [
            'getBaseFields',
        ]);

        $payment->method('getBaseFields')->willReturn([
            'base key1' => 'base value 1',
        ]);

        $payment->setSiteId(12345);
        $payment->setAmount(9.95);
        $payment->setCurrency('EUR');

        $tokenDetails = new Token();
        $tokenDetails->setToken('myPaymentDetailsToken');

        $payment->setPaymentDetails($tokenDetails);
        $payment->setExternalReferences($this->createMock(ExternalReferences::class));
        $payment->setCustomerDetails($this->createMock(Customer::class));
        $payment->setShippingAddress($this->createMock(ShippingAddress::class));
        $payment->setRequestDetails($this->createMock(ClientRequest::class));

        $actualResult = $payment->jsonSerialize();

        self::assertSame([
            'base key1' => 'base value 1',
            'site_id' => 12345,
            'amount' => 9.95,
            'currency' => 'EUR',
            'external_references' => null,
            'items' => [],
            'payment_details' => [
                'token' => 'myPaymentDetailsToken',
            ],
            'customer_details' => null,
            'shipping_address' => null,
            'request_details' => null,
            'subscription_schedule' => null,
            'mit' => false,
            'preauth_only' => false,
            'non_recurring' => false,
        ], $actualResult);
    }
}
