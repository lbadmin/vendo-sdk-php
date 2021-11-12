<?php

namespace VendoSdkUnit\Gateway;

use VendoSdk\Gateway\CreditCardPayment;
use VendoSdk\Gateway\Request\Details\CreditCard;
use VendoSdk\Gateway\Request\Details\Customer;
use VendoSdk\Gateway\Request\Details\ExternalReferences;
use VendoSdk\Gateway\Request\Details\Item;
use VendoSdk\Gateway\Request\Details\Request;
use VendoSdk\Gateway\Request\Details\ShippingAddress;

class TestCreditCardPayment extends CreditCardPayment
{
    public function getBaseFields(): array // expose protected method for testing
    {
        return parent::getBaseFields();
    }
}

class CreditCardPaymentTest extends \PHPUnit\Framework\TestCase
{
    public function testApiEndpoint()
    {
        $payment = new CreditCardPayment();
        self::assertEquals('https://secure.vend-o.com/api/gateway/payment', $payment->getApiEndpoint());
    }

    public function testSetCreditCardDetails()
    {
        $payment = $this->createPartialMock(CreditCardPayment::class, [
            'getBaseFields'
        ]);

        $payment->method('getBaseFields')->willReturn([
            'base key1' => 'base value 1',
        ]);

        $payment->setSiteId(12345);
        $payment->setIsPreAuth(true);

        $creditCardDetails = new CreditCard();
        $creditCardDetails->setCardNumber('4111111111111111');
        $creditCardDetails->setCvv('123');
        $creditCardDetails->setExpirationMonth('12');
        $payment->setCreditCardDetails($creditCardDetails);
        $creditCardDetails->setExpirationYear('2999');
        $creditCardDetails->setNameOnCard('Jan Testovitch');

        $payment->setCreditCardDetails($creditCardDetails);
        self::assertEquals($creditCardDetails, $payment->getCreditCardDetails());
        self::assertEquals([
            'base key1' => 'base value 1',
            'site_id' => 12345,
            'preauth_only' => true,
            'payment_details' => $creditCardDetails,
        ], $payment->jsonSerialize());
    }

    public function testGetBaseFields()
    {
        $externalReferences = $this->createMock(ExternalReferences::class);
        $items = [$this->createMock(Item::class)];
        $customerDetails = $this->createMock(Customer::class);
        $shippingAddress = $this->createMock(ShippingAddress::class);
        $requestDetails = $this->createMock(Request::class);

        $payment = new TestCreditCardPayment();
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
        ];

        $this->assertEquals($expectedResult, $payment->getBaseFields());
    }
}
