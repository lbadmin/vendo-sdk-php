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
        $payment = new Payment();

        $payment->setApiSecret('test-api-secret');
        $payment->setIsTest(1);
        $payment->setMerchantId(123);
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
        $payment->setSuccessUrl('http://www.somesuccessurl.com/payment');

        $actualResult = $payment->jsonSerialize();

        self::assertSame([
            'api_secret' => 'test-api-secret',
            'is_test' => 1,
            'merchant_id' => 123,
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
            'preauth_only' => false,
            'non_recurring' => false,
            'success_url' => 'http://www.somesuccessurl.com/payment',
            'mit' => false,
        ], $actualResult);
    }
}
