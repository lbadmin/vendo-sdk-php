<?php
namespace VendoSdkUnit\S2S;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\S2S\Request\CancelSubscription;
use VendoSdk\S2S\Request\CapturePayment;
use VendoSdk\S2S\Request\ChangeSubscription;
use VendoSdk\S2S\Request\SubscriptionBase;
use VendoSdk\Vendo;

class ChangeSubscriptionTest extends \PHPUnit\Framework\TestCase
{
    public function testCancelSubscriptionSuccess()
    {
        $changeSubscription = new ChangeSubscription();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
        $schedule->setNextRebillDate('2025-10-11');
        $schedule->setRebillDuration(12);//days
        $schedule->setRebillAmount(10.34);//billing currency
        $changeSubscription->setSubscriptionSchedule($schedule);

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
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/change-subscription', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":1,"request_id":234,"subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321,
    "subscription_schedule": {
        "next_rebill_date": "2025-10-11",
        "rebill_amount": 10.34,
        "rebill_duration": 12
    }
}', $changeSubscription->getRawRequest(true));
    }

    public function testChangeSubscriptionScheduleSuccess()
    {
        $changeSubscription = new ChangeSubscription();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
        $schedule->setNextRebillDate('2025-10-11');
        $schedule->setRebillDuration(12);//days
        $schedule->setRebillAmount(10.34);//billing currency
        $changeSubscription->setSubscriptionSchedule($schedule);

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
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/change-subscription', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":1,"request_id":234,"subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321,
    "subscription_schedule": {
        "next_rebill_date": "2025-10-11",
        "rebill_amount": 10.34,
        "rebill_duration": 12
    }
}', $changeSubscription->getRawRequest(true));
    }

    public function testChangeSubscriptionPaymentMethodSuccessVerificationRequired()
    {
        $changeSubscription = new ChangeSubscription();
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
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/change-subscription', $changeSubscription->getApiEndpoint());
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

    public function testChangeSubscriptionPaymentMethodSuccessCommit()
    {
        $changeSubscription = new ChangeSubscription();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $verificationDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Verification();
        $verificationDetails->setVerificationId(4481);//use verification_id returned in change-subscription-payment-details-request
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
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/change-subscription', $changeSubscription->getApiEndpoint());
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

    public function testChangeSubscriptionScheduleErrorBadParam()
    {
        $changeSubscription = new ChangeSubscription();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $schedule = new \VendoSdk\S2S\Request\Details\SubscriptionSchedule();
        $schedule->setNextRebillDate('2025-10-11');
        $schedule->setRebillDuration(12);//days
        $schedule->setRebillAmount(10.34);//billing currency
        $changeSubscription->setSubscriptionSchedule($schedule);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::S2S_STATUS_NOT_OK,
            'error' => [
                'code' => '8105',
                'message' => 'Invalid parameter xyz'
            ],
            'request_id' => 234,
            'subscription' => [
                'id' => 9876543,
            ],
        ]));
        $httpClient->method('send')->willReturn($response);

        $changeSubscription->setHttpClient($httpClient);
        $changeSubscription->postRequest();

        $this->assertEquals(true, $changeSubscription->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/change-subscription', $changeSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $changeSubscription->getApiSecret());
        $this->assertEquals(1234567, $changeSubscription->getMerchantId());
        $this->assertEquals(87654321, $changeSubscription->getSubscriptionId());
        $this->assertEquals('{"status":0,"error":{"code":"8105","message":"Invalid parameter xyz"},"request_id":234,"subscription":{"id":9876543}}', $changeSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321,
    "subscription_schedule": {
        "next_rebill_date": "2025-10-11",
        "rebill_amount": 10.34,
        "rebill_duration": 12
    }
}', $changeSubscription->getRawRequest(true));
    }
}
