<?php
namespace VendoSdkUnit\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VendoSdk\Gateway\CancelSubscription;
use VendoSdk\Gateway\CapturePayment;
use VendoSdk\Vendo;

class CancelSubscriptionTest extends \PHPUnit\Framework\TestCase
{
    public function testCancelSubscriptionSuccess()
    {
        $cancelSubscription = new CancelSubscription();
        $cancelSubscription->setIsTest(true);
        $cancelSubscription->setApiSecret('test-secret');
        $cancelSubscription->setIsTest(true);
        $cancelSubscription->setMerchantId(1234567);
        $cancelSubscription->setSubscriptionId(87654321);

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

        $cancelSubscription->setHttpClient($httpClient);
        $cancelSubscription->postRequest();

        $this->assertEquals(true, $cancelSubscription->isTest());
        $this->assertEquals(Vendo::BASE_URL . '/api/gateway/cancel-subscription', $cancelSubscription->getApiEndpoint());
        $this->assertEquals('test-secret', $cancelSubscription->getApiSecret());
        $this->assertEquals(1234567, $cancelSubscription->getMerchantId());
        $this->assertEquals(87654321, $cancelSubscription->getSubscriptionId());
        $this->assertEquals('{"status":1,"request_id":234,"subscription":{"id":9876543}}', $cancelSubscription->getRawResponse());

        $this->assertEquals('{
    "api_secret": "test-secret",
    "is_test": 1,
    "merchant_id": 1234567,
    "subscription_id": 87654321
}', $cancelSubscription->getRawRequest(true));
    }
}
