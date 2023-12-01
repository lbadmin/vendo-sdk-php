<?php

namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\UpdatePaymentMethod;
use VendoSdk\Vendo;

class ChangeSubscriptionPaymentMethodTest extends \PHPUnit\Framework\TestCase
{
    public function testChangeSubscriptionPaymentMethodSuccessNoVerificationRequired()
    {
        $changeSubscription = new UpdatePaymentMethod();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
        $ccDetails->setNameOnCard('John Doe');
        $ccDetails->setCardNumber('4111111111111111');
        $ccDetails->setExpirationMonth('05');
        $ccDetails->setExpirationYear('2029');
        $ccDetails->setCvv(123);
        $changeSubscription->setPaymentDetails($ccDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_OK,
            'request_id' => 234,
            'subscription' => [
                'id' => 9876543,
            ],
        ]));
        $httpClient->method('send')->willReturn($response);

        $changeSubscription->setHttpClient($httpClient);
        $changeSubscription->postRequest();

        $this->assertEquals(true, $changeSubscription->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/update-payment-method', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":1,"request_id":234,"subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321,
    "payment_details": {
        "payment_method": "card",
        "card_number": "4111111111111111",
        "expiration_month": "05",
        "expiration_year": "2029",
        "cvv": "123",
        "name_on_card": "John Doe"
    }
}', $changeSubscription->getRawRequest(true));
    }

    public function testChangeSubscriptionPaymentMethodSuccessVerificationRequired()
    {
        $changeSubscription = new UpdatePaymentMethod();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $ccDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\CreditCard();
        $ccDetails->setNameOnCard('John Doe');
        $ccDetails->setCardNumber('4000012892688323');
        $ccDetails->setExpirationMonth('05');
        $ccDetails->setExpirationYear('2029');
        $ccDetails->setCvv(123);
        $changeSubscription->setPaymentDetails($ccDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_VERIFICATION_REQUIRED,
            'request_id' => 234,
            'verification_id' => 12345,
            'verification_url' => 'http://host/verification_url',
            'subscription' => [
                'id' => 9876543,
            ],
        ]));
        $httpClient->method('send')->willReturn($response);

        $changeSubscription->setHttpClient($httpClient);
        $changeSubscription->postRequest();

        $this->assertEquals(true, $changeSubscription->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/update-payment-method', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":2,"request_id":234,"verification_id":12345,"verification_url":"http:\/\/host\/verification_url","subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321,
    "payment_details": {
        "payment_method": "card",
        "card_number": "4000012892688323",
        "expiration_month": "05",
        "expiration_year": "2029",
        "cvv": "123",
        "name_on_card": "John Doe"
    }
}', $changeSubscription->getRawRequest(true));
    }

    public function testChangeSubscriptionPaymentMethodSuccessVerificationRequest()
    {
        $changeSubscription = new UpdatePaymentMethod();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $verificationDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Verification();
        $verificationDetails->setVerificationId(4481);//use verification_id returned in update-payment-method-request
        $changeSubscription->setPaymentDetails($verificationDetails);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_OK,
            'request_id' => 234,
            'subscription' => [
                'id' => 9876543,
            ],
        ]));
        $httpClient->method('send')->willReturn($response);

        $changeSubscription->setHttpClient($httpClient);
        $changeSubscription->postRequest();

        $this->assertEquals(true, $changeSubscription->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/update-payment-method', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":1,"request_id":234,"subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321,
    "payment_details": {
        "verification_id": 4481
    }
}', $changeSubscription->getRawRequest(true));
    }
}
