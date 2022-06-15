<?php
namespace VendoSdkUnit\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Gateway\CancelSubscription;
use VendoSdk\Gateway\CapturePayment;
use VendoSdk\Gateway\ChangeSubscription;
use VendoSdk\Gateway\SubscriptionBase;
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

        $schedule = new \VendoSdk\Gateway\Request\Details\SubscriptionSchedule();
        $schedule->setNextRebillDate('2025-10-11');
        $schedule->setRebillDuration(12);//days
        $schedule->setRebillAmount(10.34);//billing currency
        $changeSubscription->setSubscriptionSchedule($schedule);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_OK,
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

    public function testCancelSubscriptionErrorBadParam()
    {
        $changeSubscription = new ChangeSubscription();
        $changeSubscription->setIsTest(true);
        $changeSubscription->setApiSecret('test-secret');
        $changeSubscription->setIsTest(true);
        $changeSubscription->setMerchantId(1234567);
        $changeSubscription->setSubscriptionId(87654321);

        $schedule = new \VendoSdk\Gateway\Request\Details\SubscriptionSchedule();
        $schedule->setNextRebillDate('2025-10-11');
        $schedule->setRebillDuration(12);//days
        $schedule->setRebillAmount(10.34);//billing currency
        $changeSubscription->setSubscriptionSchedule($schedule);

        $httpClient = $this->createMock(Client::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn(json_encode([
            'status' => Vendo::GATEWAY_STATUS_NOT_OK,
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
