<?php

namespace VendoSdkUnit\Gateway;

use VendoSdk\Gateway\Payment;
use VendoSdk\Gateway\Request\Details\CreditCard;
use VendoSdk\Gateway\Request\Details\Customer;
use VendoSdk\Gateway\Request\Details\ExternalReferences;
use VendoSdk\Gateway\Request\Details\Item;
use VendoSdk\Gateway\Request\Details\Request;
use VendoSdk\Gateway\Request\Details\ShippingAddress;

class CreditCardPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testApiEndpoint()
    {
        $payment = new Payment();
        self::assertEquals('https://secure.vend-o.com/api/gateway/payment', $payment->getApiEndpoint());
    }

    public function testGetBaseFields()
    {
        $externalReferences = $this->createMock(ExternalReferences::class);
        $items = [$this->createMock(Item::class)];
        $customerDetails = $this->createMock(Customer::class);
        $shippingAddress = $this->createMock(ShippingAddress::class);
        $requestDetails = $this->createMock(Request::class);

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
        $payment->setIsPreAuth(true);

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
            'external_references' => $externalReferences,
            'items' => $items,
            'customer_details' => $customerDetails,
            'shipping_address' => $shippingAddress,
            'request_details' => $requestDetails,
            'mit' => false,
            'site_id' => 123,
            'payment_details' => [
                'payment_method' => 'card',
                'card_number' => '4111111111111111',
                'name_on_card' => 'Joe Doe',
            ],
            'preauth_only' => true,
            'non_recurring' => false,
        ];

        $this->assertEquals($expectedResult, $payment->jsonSerialize());
    }
}
