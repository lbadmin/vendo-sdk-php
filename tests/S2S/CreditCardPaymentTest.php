<?php

namespace VendoSdkUnit\S2S;

use VendoSdk\S2S\Request\Payment;
use VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard;
use VendoSdk\S2S\Request\Details\Customer;
use VendoSdk\S2S\Request\Details\ExternalReferences;
use VendoSdk\S2S\Request\Details\Item;
use VendoSdk\S2S\Request\Details\ClientRequest;
use VendoSdk\S2S\Request\Details\ShippingAddress;

class CreditCardPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testGetBaseFields()
    {
        $externalReferences = $this->createMock(ExternalReferences::class);
        $items = [$this->createMock(Item::class)];
        $customerDetails = $this->createMock(Customer::class);
        $shippingAddress = $this->createMock(ShippingAddress::class);
        $requestDetails = $this->createMock(ClientRequest::class);

        $payment = new Payment();
        $payment->setApiSecret('test-api-secret');
        $payment->setIsTest(1);
        $payment->setMerchantId(123);
        $payment->setAmount(12.34);
        $payment->setCurrency('USD');
        $payment->setExternalReferences($externalReferences);
        $payment->setShippingAddress($shippingAddress);
        $payment->setCustomerDetails($customerDetails);
        $payment->setRequestDetails($requestDetails);
        $payment->setItems($items);
        $payment->setSiteId(123);
        $payment->setPreAuthOnly(true);
        $payment->setSuccessUrl('http://www.somesuccessurl.com/payment');

        $paymentDetails = $this->createPartialMock(CreditCard::class, [
            'jsonSerialize',
        ]);
        $paymentDetails->method('jsonSerialize')->willReturn([
           'payment_method' => 'card',
           'card_number' => '4111111111111111',
           'name_on_card' => 'Joe Doe',
        ]);
        $payment->setPaymentDetails($paymentDetails);

        $expectedResult = [
            'api_secret' => 'test-api-secret',
            'is_test' => 1,
            'merchant_id' => 123,
            'amount' => 12.34,
            'currency' => 'USD',
            'external_references' => null,
            'items' => $items,
            'customer_details' => null,
            'shipping_address' => null,
            'request_details' => null,
            'mit' => false,
            'site_id' => 123,
            'payment_details' => [
                'payment_method' => 'card',
                'card_number' => '4111111111111111',
                'name_on_card' => 'Joe Doe',
            ],
            'preauth_only' => true,
            'non_recurring' => false,
            'subscription_schedule' => null,
            'success_url' => 'http://www.somesuccessurl.com/payment',
        ];

        $this->assertEquals($expectedResult, $payment->jsonSerialize());
    }
}
